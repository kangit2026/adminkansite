<?php

// Аналітика сайту на дашборді адмінки (App\Services\SiteAnalytics).
// Відвідуваність і події сайту беруться з джерела; заявки й відгуки — завжди з бази адмінки.
return [
    // demo — правдоподібні демо-цифри з позначкою «Демо-дані» на дашборді;
    // ga4  — реальні дані Google Analytics (підключимо під час запуску сайту, див. Ga4AnalyticsSource)
    'source' => env('ANALYTICS_SOURCE', 'demo'),

    // часовий пояс для меж періоду й підписів днів/тижнів (має збігатися з поясом ресурсу GA4)
    'timezone' => env('ANALYTICS_TIMEZONE', 'Europe/Kyiv'),

    // для справжнього джерела: скільки секунд тримати відповідь у кеші і скільки чекати після помилки
    'cache_ttl' => 3600,
    'error_ttl' => 600,

    'ga4' => [
        // числовий ID ресурсу GA4 (не G-XXXX)
        'property_id' => env('GA4_PROPERTY_ID'),
        // JSON-ключ сервісного акаунта — файл на сервері поза git: абсолютний шлях або відносно кореня проєкту
        'credentials' => env('GA4_CREDENTIALS_PATH', 'storage/app/private/ga4-service-account.json'),
    ],
];
