<?php

namespace App\Services;

use App\Models\Vacancy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Просить сайт (Next.js) одразу перебудувати сторінки кар'єри після змін у вакансіях,
 * не чекаючи власного таймера оновлення. Без SITE_REVALIDATE_URL нічого не робить;
 * помилка сайту не заважає збереженню в адмінці — лише пишеться в лог.
 */
class SiteRevalidator
{
    public function career(?Vacancy $vacancy = null): void
    {
        $url = config('services.site.revalidate_url');

        if (! $url) {
            return;
        }

        $paths = ['/career'];
        if ($vacancy) {
            $paths[] = '/career/'.$vacancy->slug;
        }

        $secret = (string) config('services.site.revalidate_secret');

        dispatch(function () use ($url, $secret, $paths) {
            try {
                Http::timeout(5)->withToken($secret)->post($url, ['paths' => $paths])->throw();
            } catch (\Throwable $e) {
                Log::warning('Site revalidate failed: '.$e->getMessage(), ['paths' => $paths]);
            }
        })->afterResponse();
    }
}
