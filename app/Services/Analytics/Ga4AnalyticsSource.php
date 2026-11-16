<?php

namespace App\Services\Analytics;

use LogicException;

/**
 * Реальні дані з Google Analytics 4 — підключимо під час запуску сайту (задача в Notion).
 * Поки що заглушка: з ANALYTICS_SOURCE=ga4 дашборд показує картку «Google Analytics не підключено».
 *
 * TODO(GA4) під час запуску:
 *  - доступ: сервісний акаунт з роллю Viewer на ресурсі config('analytics.ga4.property_id'),
 *    JSON-ключ — файлом config('analytics.ga4.credentials') поза git;
 *  - isAvailable(): є ID ресурсу і ключ читається;
 *  - fetch(): GA4 Data API properties/{id}:runReport (пакет google/analytics-data або HTTP + JWT сервісного акаунта),
 *    dateRanges — поточний і попередній періоди, і перетворення відповіді у формат SiteAnalyticsSource::fetch():
 *      totals — metrics activeUsers, sessions;
 *      events — dimension eventName, metric eventCount, inListFilter по ключах SiteAnalytics::EVENTS.
 *    Кеш на годину і паузу після помилок уже робить SiteAnalytics.
 */
class Ga4AnalyticsSource implements SiteAnalyticsSource
{
    public function isDemo(): bool
    {
        return false;
    }

    public function isAvailable(): bool
    {
        return false;
    }

    public function fetch(AnalyticsPeriod $period): array
    {
        throw new LogicException('GA4 analytics source is not implemented yet.');
    }
}
