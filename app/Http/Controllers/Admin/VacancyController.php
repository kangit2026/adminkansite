<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerDirection;
use App\Models\Vacancy;
use App\Services\SiteRevalidator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VacancyController extends Controller
{
    /** Активні вгорі, потім чернетки, закриті — у кінці. */
    private const STATUS_ORDER = "CASE status WHEN 'active' THEN 0 WHEN 'draft' THEN 1 ELSE 2 END";

    public function __construct(private SiteRevalidator $site) {}

    public function index(Request $request)
    {
        $status = $request->query('status');
        $status = array_key_exists((string) $status, Vacancy::STATUSES) ? $status : null;

        $vacancies = Vacancy::query()
            ->with('direction')
            ->withCount([
                'candidates',
                'candidates as new_candidates_count' => fn ($q) => $q->where('status', 'new'),
            ])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByRaw(self::STATUS_ORDER)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        $counts = Vacancy::query()->selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        return view('admin.vacancies.index', compact('vacancies', 'status', 'counts'));
    }

    public function create()
    {
        $vacancy = new Vacancy(['employment_type' => 'full_time', 'status' => 'draft']);

        return view('admin.vacancies.create', [
            'vacancy' => $vacancy,
            'directions' => CareerDirection::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = ($data['slug'] ?? null) ?: Vacancy::uniqueSlug($data['title']);
        $publish = $request->input('action') === 'publish';

        $vacancy = Vacancy::create($data + [
            'status' => $publish ? 'active' : 'draft',
            'published_at' => $publish ? now() : null,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        if ($publish) {
            $this->site->career($vacancy);
        }

        return redirect()
            ->route('admin.vacancies.edit', $vacancy)
            ->with('status', $publish ? 'Вакансію опубліковано.' : 'Чернетку збережено.');
    }

    public function edit(Vacancy $vacancy)
    {
        $vacancy->loadCount([
            'candidates',
            'candidates as new_candidates_count' => fn ($q) => $q->where('status', 'new'),
        ]);

        return view('admin.vacancies.edit', [
            'vacancy' => $vacancy,
            'directions' => CareerDirection::ordered()->get(),
        ]);
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $data = $this->validated($request, $vacancy);

        if ($vacancy->slugLocked()) {
            unset($data['slug']);
        } else {
            $data['slug'] = ($data['slug'] ?? null) ?: Vacancy::uniqueSlug($data['title'], $vacancy->id);
        }

        $vacancy->update($data + ['updated_by' => $request->user()->id]);

        if ($vacancy->status === 'active') {
            $this->site->career($vacancy);
        }

        return back()->with('status', 'Вакансію збережено.');
    }

    public function destroy(Vacancy $vacancy)
    {
        if ($vacancy->status !== 'draft' || $vacancy->candidates()->exists()) {
            return back()->withErrors(['vacancy' => 'Видалити можна лише чернетку без відгуків. Опубліковану вакансію закрийте.']);
        }

        $vacancy->delete();

        return redirect()->route('admin.vacancies.index')->with('status', 'Чернетку видалено.');
    }

    public function publish(Request $request, Vacancy $vacancy)
    {
        return $this->switchStatus($request, $vacancy, 'active', 'Вакансію опубліковано.');
    }

    public function close(Request $request, Vacancy $vacancy)
    {
        return $this->switchStatus($request, $vacancy, 'closed', 'Вакансію закрито. Відгуки збережено.');
    }

    public function reopen(Request $request, Vacancy $vacancy)
    {
        return $this->switchStatus($request, $vacancy, 'active', 'Вакансію знову опубліковано.');
    }

    private function switchStatus(Request $request, Vacancy $vacancy, string $status, string $message)
    {
        if ($vacancy->status !== $status) {
            $vacancy->update($status === 'active'
                // і перша публікація, і повторна — свіжа дата на сайті
                ? ['status' => 'active', 'published_at' => now(), 'closed_at' => null, 'updated_by' => $request->user()->id]
                : ['status' => 'closed', 'closed_at' => now(), 'updated_by' => $request->user()->id]);

            $this->site->career($vacancy);
        }

        return back()->with('status', $message);
    }

    private function validated(Request $request, ?Vacancy $vacancy = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('vacancies', 'slug')->ignore($vacancy?->id)],
            'direction_id' => ['required', 'integer', 'exists:career_directions,id'],
            'project' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', Rule::in(array_keys(Vacancy::EMPLOYMENT_TYPES))],
            'place' => ['nullable', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:300'],
            'about' => ['required', 'string', 'max:5000'],
            'tasks' => ['required', 'array', 'max:30'],
            'requirements' => ['nullable', 'array', 'max:30'],
            'nice_to_have' => ['nullable', 'array', 'max:30'],
            'offer' => ['nullable', 'array', 'max:30'],
            'tasks.*' => ['nullable', 'string', 'max:500'],
            'requirements.*' => ['nullable', 'string', 'max:500'],
            'nice_to_have.*' => ['nullable', 'string', 'max:500'],
            'offer.*' => ['nullable', 'string', 'max:500'],
        ], [
            'required' => 'Заповніть поле «:attribute».',
            'max.string' => 'Пункт у полі «:attribute» задовгий — до :max символів. Розбийте його на два.',
            'max.array' => 'У полі «:attribute» забагато пунктів — до :max.',
            'max' => 'Поле «:attribute» задовге — до :max символів.',
            'slug.regex' => 'Адреса сторінки — лише малі латинські літери, цифри й дефіси.',
            'slug.unique' => 'Така адреса вже є в іншої вакансії.',
            'exists' => 'Оберіть напрям зі списку.',
            'in' => 'Оберіть значення зі списку.',
        ], [
            'title' => 'Назва посади',
            'slug' => 'Адреса сторінки',
            'direction_id' => 'Напрям',
            'project' => 'Проєкт',
            'employment_type' => 'Тип зайнятості',
            'place' => 'Місце роботи',
            'summary' => 'Коротко для картки',
            'about' => 'Коротко про роль',
            'tasks' => 'Чим ви займатиметеся',
            'requirements' => 'Що для нас важливо',
            'nice_to_have' => 'Буде перевагою',
            'offer' => 'Що ми пропонуємо',
        ]);

        // списки: кожне непорожнє поле — пункт; маркери «•», «-», «—» на початку прибираємо
        foreach (Vacancy::LISTS as $field) {
            $lines = array_map(fn ($line) => trim(preg_replace('/^\s*[•\-—–*]\s*/u', '', (string) $line)), $data[$field] ?? []);
            $data[$field] = array_values(array_filter($lines, fn (string $line) => $line !== '')) ?: null;
        }

        if (! $data['tasks']) {
            throw ValidationException::withMessages(['tasks' => 'Додайте хоча б один пункт у полі «Чим ви займатиметеся».']);
        }

        return $data;
    }
}
