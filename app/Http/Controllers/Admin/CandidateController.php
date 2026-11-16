<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        // лічильники на вкладках статусів — з урахуванням інших фільтрів
        $counts = $this->filtered($filters)->selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        $candidates = $this->filtered($filters)
            ->when($filters['status'], fn ($q, $status) => $q->where('status', $status))
            ->with('responsible')
            ->latest()
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.candidates.index', [
            'candidates' => $candidates,
            'counts' => $counts,
            'filters' => $filters,
            'vacancies' => Vacancy::query()->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'draft' THEN 1 ELSE 2 END")->orderByDesc('published_at')->get(['id', 'title', 'status']),
            'users' => $this->staff(),
        ]);
    }

    public function show(Candidate $candidate)
    {
        $candidate->load(['vacancy.direction', 'responsible', 'notes.user']);

        return view('admin.candidates.show', [
            'candidate' => $candidate,
            'users' => $this->staff(),
        ]);
    }

    /** Статус і відповідальний — окремими формами, кожна змінює одне поле й лишає запис у стрічці. */
    public function update(Request $request, Candidate $candidate)
    {
        if ($request->has('status')) {
            $status = $request->validate([
                'status' => ['required', Rule::in(array_keys(Candidate::STATUSES))],
            ])['status'];

            if ($status !== $candidate->status) {
                $from = $candidate->statusLabel();
                $candidate->update(['status' => $status]);
                $candidate->addNote('Статус: '.$from.' → '.$candidate->statusLabel(), $request->user(), true);
            }

            return back()->with('status', 'Статус оновлено.');
        }

        $responsibleId = $request->validate([
            'responsible_id' => ['nullable', 'integer', Rule::in($this->staff()->pluck('id'))],
        ])['responsible_id'] ?? null;

        if ((int) $responsibleId !== (int) $candidate->responsible_id) {
            $candidate->update(['responsible_id' => $responsibleId]);
            $candidate->load('responsible');
            $candidate->addNote(
                $candidate->responsible ? 'Відповідальний: '.$candidate->responsible->name : 'Відповідального знято',
                $request->user(),
                true,
            );
        }

        return back()->with('status', 'Відповідального оновлено.');
    }

    public function storeNote(Request $request, Candidate $candidate)
    {
        $data = $request->validate(
            ['text' => ['required', 'string', 'max:5000']],
            ['text.required' => 'Напишіть текст нотатки.', 'text.max' => 'Нотатка задовга — до 5000 символів.'],
        );

        $candidate->addNote($data['text'], $request->user());

        return back()->with('status', 'Нотатку додано.');
    }

    public function cv(Candidate $candidate)
    {
        abort_unless($candidate->cv_path && Storage::disk(Candidate::CV_DISK)->exists($candidate->cv_path), 404);

        return Storage::disk(Candidate::CV_DISK)->download($candidate->cv_path, $candidate->cvDownloadName());
    }

    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return redirect()->route('admin.candidates.index')->with('status', 'Кандидата й резюме видалено.');
    }

    /** CSV з поточними фільтрами: «;» і BOM — щоб Excel з українською локаллю відкрив кирилицю по колонках. */
    public function export(Request $request)
    {
        $filters = $this->filters($request);
        $query = $this->filtered($filters)
            ->when($filters['status'], fn ($q, $status) => $q->where('status', $status))
            ->with(['vacancy.direction', 'responsible']);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'Дата і час', 'ПІБ', 'Телефон', 'Email', 'LinkedIn', 'Вакансія', 'Напрям', 'Проєкт',
                'Тип зайнятості', 'Коментар кандидата', 'Джерело', 'Статус', 'Відповідальний', 'Остання дія', 'Картка в адмінці',
            ], ';');

            $query->latest()->latest('id')->chunk(500, function ($candidates) use ($out) {
                foreach ($candidates as $c) {
                    fputcsv($out, array_map(fn ($value) => $this->csvCell($value), [
                        $c->created_at->format('d.m.Y H:i'),
                        $c->name,
                        $c->phone,
                        $c->email,
                        $c->linkedin,
                        $c->vacancyLabel(),
                        $c->vacancy?->direction?->name ?? $c->direction,
                        $c->vacancy?->project,
                        $c->vacancy?->employmentLabel(),
                        $c->message,
                        'Сайт',
                        $c->statusLabel(),
                        $c->responsible?->name,
                        $c->last_activity_at?->format('d.m.Y H:i'),
                        route('admin.candidates.show', $c),
                    ]), ';');
                }
            });

            fclose($out);
        }, 'kandydaty-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Текст кандидата не має стати формулою в Excel: «=…», «+…», «-…», «@…» — з апострофом. */
    private function csvCell(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }

    private function filters(Request $request): array
    {
        $status = (string) $request->query('status', '');

        return [
            'status' => array_key_exists($status, Candidate::STATUSES) ? $status : null,
            'vacancy' => (string) $request->query('vacancy', ''),
            'responsible' => (string) $request->query('responsible', ''),
            'q' => trim((string) $request->query('q', '')),
        ];
    }

    /** Усі фільтри, крім статусу. */
    private function filtered(array $f): Builder
    {
        return Candidate::query()
            ->when($f['vacancy'] === 'general', fn ($q) => $q->whereNull('vacancy_id'))
            ->when(ctype_digit($f['vacancy']), fn ($q) => $q->where('vacancy_id', (int) $f['vacancy']))
            ->when($f['responsible'] === 'none', fn ($q) => $q->whereNull('responsible_id'))
            ->when(ctype_digit($f['responsible']), fn ($q) => $q->where('responsible_id', (int) $f['responsible']))
            ->when($f['q'] !== '', function ($q) use ($f) {
                $like = '%'.addcslashes($f['q'], '%_\\').'%';
                $digits = preg_replace('/\D/', '', $f['q']);

                $q->where(function ($w) use ($like, $digits) {
                    $w->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone', 'like', $like);

                    // телефон шукаємо за цифрами: «067 000 14 08» знайде «+380 67 000 14 08»
                    if (strlen($digits) >= 3) {
                        $w->orWhereRaw(
                            "REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') LIKE ?",
                            ['%'.$digits.'%'],
                        );
                    }
                });
            });
    }

    /** Кого можна призначити відповідальним: увімкнені HR та адміністратори. */
    private function staff()
    {
        return User::role(['admin', 'hr'])->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }
}
