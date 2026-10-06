<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RecaptchaVerifier
{
    public function verify(?string $token, ?string $remoteIp = null): void
    {
        if (! config('recaptcha.enabled')) {
            return;
        }

        if (! $token) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['Підтвердіть, що ви не робот.'],
            ]);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('recaptcha.secret_key'),
            'response' => $token,
            'remoteip' => $remoteIp,
        ]);

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['Не вдалося перевірити reCAPTCHA. Спробуйте пізніше.'],
            ]);
        }

        $body = $response->json();

        if (! ($body['success'] ?? false)) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['Перевірку reCAPTCHA не пройдено.'],
            ]);
        }

        $score = (float) ($body['score'] ?? 0);
        $minScore = (float) config('recaptcha.min_score', 0.5);

        if ($score < $minScore) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['Перевірку reCAPTCHA не пройдено.'],
            ]);
        }
    }
}
