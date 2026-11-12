<?php

// Cloudflare Turnstile — захист форм сайту від ботів. Без TURNSTILE_SECRET_KEY перевірка вимкнена
// (локальна розробка); на продакшні ключ обов’язковий. Тестові ключі Cloudflare, що завжди проходять:
// site 1x00000000000000000000AA, secret 1x0000000000000000000000000000000AA.
return [
    'site_key' => env('TURNSTILE_SITE_KEY'),
    'secret_key' => env('TURNSTILE_SECRET_KEY'),
    'enabled' => filled(env('TURNSTILE_SECRET_KEY')),
    // сторінки сайту, з яких приймаємо токен; порожньо — не перевіряти hostname
    'hostnames' => array_values(array_filter(array_map('trim', explode(',', (string) env('TURNSTILE_HOSTNAMES', ''))))),
];
