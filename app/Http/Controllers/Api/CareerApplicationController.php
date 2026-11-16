<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewCandidateMail;
use App\Models\Candidate;
use App\Models\CareerPage;
use App\Models\Vacancy;
use App\Services\TurnstileVerifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Відгук із сайту (multipart): на вакансію (поле vacancy = slug) або анкета до загальної бази.
 * Кандидат і резюме — в адмінці, HR отримує лист.
 */
class CareerApplicationController extends Controller
{
    /** Успішних відгуків з однієї адреси за годину; помилки валідації не рахуються. */
    private const PER_HOUR = 5;

    private const PHONE_ERROR = 'Вкажіть номер у форматі +380 XX XXX XX XX.';

    public function store(Request $request, TurnstileVerifier $turnstile)
    {
        // пастка для ботів: людина цього поля не бачить; відповідь та сама, що й на справжній відгук
        if ($request->filled('website')) {
            return response()->json(['success' => true], 201);
        }

        // «linkedin.com/in/…» без https:// — теж посилання
        if ($request->filled('linkedin') && ! preg_match('#^https?://#i', (string) $request->input('linkedin'))) {
            $request->merge(['linkedin' => 'https://'.ltrim((string) $request->input('linkedin'), '/')]);
        }

        $data = $request->validate([
            'vacancy' => ['nullable', 'string', 'exists:vacancies,slug'],
            'direction' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'max:50', function (string $attribute, mixed $value, Closure $fail) {
                $digits = strlen(preg_replace('/\D/', '', $value));
                if ($digits < 10 || $digits > 12 || preg_match('/[^\d\s+\-()]/', $value)) {
                    $fail(self::PHONE_ERROR);
                }
            }],
            'email' => ['required', 'email', 'max:255'],
            'linkedin' => ['nullable', 'url:http,https', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'cv' => ['nullable', 'file', 'max:10240', 'extensions:pdf,doc,docx,rtf,odt,jpg,jpeg,png', 'mimes:pdf,doc,docx,rtf,odt,jpg,jpeg,png'],
            'consent' => ['accepted'],
            'turnstile_token' => ['nullable', 'string', 'max:2048'],
        ], [
            'vacancy.exists' => 'Вакансію не знайдено. Оновіть сторінку й спробуйте ще раз.',
            'name.required' => 'Вкажіть ім’я та прізвище — так ми знатимемо, як до вас звертатися.',
            'name.min' => 'Вкажіть ім’я та прізвище — так ми знатимемо, як до вас звертатися.',
            'phone.required' => self::PHONE_ERROR,
            'email.required' => 'Вкажіть email у форматі name@example.com.',
            'email.email' => 'Вкажіть email у форматі name@example.com.',
            'linkedin.url' => 'Вкажіть посилання на профіль LinkedIn або залиште поле порожнім.',
            'message.max' => 'Коментар задовгий — до 5000 символів.',
            'cv.file' => 'Додайте резюме у форматі PDF, DOC, DOCX, RTF, ODT, JPG або PNG.',
            'cv.extensions' => 'Додайте резюме у форматі PDF, DOC, DOCX, RTF, ODT, JPG або PNG.',
            'cv.mimes' => 'Додайте резюме у форматі PDF, DOC, DOCX, RTF, ODT, JPG або PNG.',
            'cv.max' => 'Файл більший за 10 МБ — стисніть його або надішліть інший.',
            'cv.uploaded' => 'Файл більший за 10 МБ — стисніть його або надішліть інший.',
            'consent.accepted' => 'Потрібна згода на обробку даних, щоб ми могли розглянути анкету.',
            'max' => 'Задовге значення.',
        ]);

        $turnstile->verify($data['turnstile_token'] ?? null, $request->ip());

        $limitKey = 'career-apply-ok:'.$request->ip();
        if (RateLimiter::tooManyAttempts($limitKey, self::PER_HOUR)) {
            return response()->json(['message' => 'Забагато спроб. Спробуйте пізніше.'], 429);
        }

        $vacancy = ! empty($data['vacancy'])
            ? Vacancy::with('direction')->where('slug', $data['vacancy'])->first()
            : null;

        $cv = $request->file('cv');
        $cvPath = $cv?->storeAs(
            'cv/'.now()->format('Y/m'),
            Str::uuid().'.'.strtolower($cv->getClientOriginalExtension()),
            Candidate::CV_DISK,
        );

        $candidate = Candidate::create([
            'vacancy_id' => $vacancy?->id,
            'vacancy_title' => $vacancy?->title,
            'direction' => $vacancy?->direction?->name ?? (isset($data['direction']) ? Str::squish($data['direction']) : null),
            'name' => Str::squish($data['name']),
            'phone' => Str::squish($data['phone']),
            'email' => $data['email'],
            'linkedin' => $data['linkedin'] ?? null,
            'message' => $data['message'] ?? null,
            'cv_path' => $cvPath ?: null,
            'cv_original_name' => $cv ? Str::limit($cv->getClientOriginalName(), 250, '') : null,
            'cv_size' => $cv?->getSize(),
            'status' => 'new',
            'last_activity_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        $candidate->notes()->create(['text' => 'Відгук надійшов із сайту.', 'is_system' => true]);

        RateLimiter::hit($limitKey, 3600);

        $this->notifyHr($candidate);

        return response()->json(['success' => true], 201);
    }

    /** Лист HR — після відповіді сайту, щоб кандидат не чекав на пошту; збій пошти лише в лог. */
    private function notifyHr(Candidate $candidate): void
    {
        $emails = CareerPage::current()->notifyEmailList();

        if (! $emails) {
            return;
        }

        dispatch(function () use ($emails, $candidate) {
            try {
                Mail::to($emails)->send(new NewCandidateMail($candidate));
            } catch (\Throwable $e) {
                Log::error('HR notification failed: '.$e->getMessage(), ['candidate_id' => $candidate->id]);
            }
        })->afterResponse();
    }
}
