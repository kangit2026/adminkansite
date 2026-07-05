<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * Фронтенд шле Accept-Language рівно як "ua" (для укр. локалі "uk")
     * або "en" — без якостей/списків. Мапимо "ua" на внутрішню локаль "uk",
     * якою збережені переклади в БД (spatie/laravel-translatable).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = strtolower((string) $request->header('Accept-Language', ''));

        $locale = $header === 'en' ? 'en' : 'uk';

        app()->setLocale($locale);

        return $next($request);
    }
}
