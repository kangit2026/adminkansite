<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Support\HomeQuiz;
use App\Services\SubmissionNotifier;
use App\Services\TurnstileVerifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Заявки з форм сайту, крім зворотного зв’язку й відгуків на вакансії:
 * пропозиція ділянки (multipart із файлами), заявка на квартиру, презентація на email.
 */
class LeadController extends Controller
{
    private const PHONE_ERROR = 'Вкажіть номер у форматі +380 XX XXX XX XX.';

    private const LAND_MAX_FILES = 10;

    private const LAND_MAX_BYTES = 25 * 1024 * 1024;

    public function __construct(private TurnstileVerifier $turnstile, private SubmissionNotifier $notifier) {}

    public function land(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json(['success' => true], 201);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50', $this->phoneRule()],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'min:3', 'max:500'],
            'area' => ['nullable', 'numeric', 'gt:0'],
            'cadastre' => ['nullable', 'regex:/^\d{10}:\d{2}:\d{3}:\d{4}$/'],
            'message' => ['nullable', 'string', 'max:5000'],
            'docs' => ['nullable', 'array', 'max:'.self::LAND_MAX_FILES],
            'docs.*' => ['file', 'max:25600', 'extensions:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'consent' => ['accepted'],
            'turnstile_token' => ['nullable', 'string', 'max:2048'],
        ], [
            'docs.max' => 'Забагато файлів — не більше '.self::LAND_MAX_FILES.'.',
            'docs.*.extensions' => 'Додайте файли у форматі PDF, DOC, DOCX, XLS, XLSX, JPG або PNG.',
            'docs.*.mimes' => 'Додайте файли у форматі PDF, DOC, DOCX, XLS, XLSX, JPG або PNG.',
            'docs.*.max' => 'Файл більший за 25 МБ — стисніть його або надішліть інший.',
        ]);

        $files = array_values(array_filter((array) $request->file('docs')));
        if (array_sum(array_map(fn ($f) => $f->getSize(), $files)) > self::LAND_MAX_BYTES) {
            throw ValidationException::withMessages(['docs' => ['Файли разом більші за 25 МБ.']]);
        }

        $this->turnstile->verify($data['turnstile_token'] ?? null, $request->ip());

        $attachments = [];
        foreach ($files as $file) {
            $path = $file->storeAs(
                'land/'.now()->format('Y/m'),
                Str::uuid().'.'.strtolower($file->getClientOriginalExtension()),
                ContactSubmission::FILES_DISK,
            );
            $attachments[] = [
                'path' => $path,
                'name' => Str::limit($file->getClientOriginalName(), 250, ''),
                'size' => $file->getSize(),
            ];
        }

        return $this->store('land', $request, [
            'name' => Str::squish($data['name']),
            'phone' => Str::squish($data['phone']),
            'email' => $data['email'],
            'message' => $data['message'] ?? null,
            'payload' => array_filter([
                'Компанія' => $data['company'] ?? null,
                'Адреса ділянки' => Str::squish($data['address']),
                'Площа, га' => $data['area'] ?? null,
                'Кадастровий номер' => $data['cadastre'] ?? null,
            ], fn ($v) => filled($v)),
            'attachments' => $attachments ?: null,
            'source_page' => $data['source_page'] ?? null,
        ], 201);
    }

    public function apartment(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json(['success' => true]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'max:50', $this->phoneRule()],
            'type' => ['nullable', 'string', 'max:100'],
            'project' => ['nullable', 'string', 'max:100'],
            'quiz' => ['nullable', 'string', 'max:2000'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'consent' => ['accepted'],
            'turnstile_token' => ['nullable', 'string', 'max:2048'],
        ]);

        $this->turnstile->verify($data['turnstile_token'] ?? null, $request->ip());

        // квіз на головній шле відповіді як JSON {id кроку: значення} — кожна відповідь стає окремим полем заявки;
        // решта форм (сторінки проєктів) — як раніше: проєкт і тип одним рядком
        $answers = isset($data['quiz']) ? json_decode($data['quiz'], true) : null;
        $payload = is_array($answers) && $answers
            ? HomeQuiz::payloadFor($answers)
            : [
                'Проєкт' => $data['project'] ?? null,
                'Тип / вибір' => $data['type'] ?? null,
            ];

        return $this->store('apartment', $request, [
            'name' => Str::squish($data['name']),
            'phone' => Str::squish($data['phone']),
            'payload' => array_filter($payload, fn ($v) => filled($v)),
            'source_page' => $data['source_page'] ?? null,
        ]);
    }

    public function deck(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json(['success' => true]);
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'project' => ['nullable', 'string', 'max:100'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'consent' => ['accepted'],
            'turnstile_token' => ['nullable', 'string', 'max:2048'],
        ]);

        $this->turnstile->verify($data['turnstile_token'] ?? null, $request->ip());

        return $this->store('deck', $request, [
            'email' => $data['email'],
            'payload' => array_filter(['Проєкт' => $data['project'] ?? null], fn ($v) => filled($v)),
            'source_page' => $data['source_page'] ?? null,
        ]);
    }

    private function store(string $type, Request $request, array $fields, int $status = 200)
    {
        $submission = ContactSubmission::create($fields + [
            'type' => $type,
            'payload' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'new',
        ]);

        $this->notifier->notify($submission);

        return response()->json(['success' => true], $status);
    }

    private function phoneRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            $digits = strlen(preg_replace('/\D/', '', $value));
            if ($digits < 10 || $digits > 12 || preg_match('/[^\d\s+\-()]/', $value)) {
                $fail(self::PHONE_ERROR);
            }
        };
    }
}
