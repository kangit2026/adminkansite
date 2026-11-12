<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/** Серверна перевірка токена Cloudflare Turnstile. Токен одноразовий, живе 5 хвилин. */
class TurnstileVerifier
{
    private const URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function verify(?string $token, ?string $remoteIp = null): void
    {
        if (! config('turnstile.enabled')) {
            return;
        }

        if (! $token) {
            $this->fail('Підтвердіть, що ви не робот.');
        }

        try {
            $response = Http::asForm()->timeout(8)->post(self::URL, [
                'secret' => config('turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $remoteIp,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Turnstile siteverify unreachable: '.$e->getMessage());
            $this->fail('Не вдалося перевірити захист від ботів. Спробуйте пізніше.');
        }

        if (! $response->successful()) {
            Log::warning('Turnstile siteverify HTTP '.$response->status());
            $this->fail('Не вдалося перевірити захист від ботів. Спробуйте пізніше.');
        }

        $body = $response->json();

        if (! ($body['success'] ?? false)) {
            Log::info('Turnstile rejected', ['codes' => $body['error-codes'] ?? []]);
            $this->fail('Перевірку не пройдено. Оновіть сторінку й спробуйте ще раз.');
        }

        $hostnames = config('turnstile.hostnames');
        if ($hostnames && ! in_array($body['hostname'] ?? '', $hostnames, true)) {
            Log::warning('Turnstile hostname mismatch', ['hostname' => $body['hostname'] ?? null]);
            $this->fail('Перевірку не пройдено. Оновіть сторінку й спробуйте ще раз.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['turnstile_token' => [$message]]);
    }
}
