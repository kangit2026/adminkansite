<?php

namespace App\Services\Analytics;

/**
 * Джерело даних про відвідуваність сайту для дашборда (config analytics.source: demo | ga4).
 * Плитки, відсотки змін і підписи робить App\Services\SiteAnalytics — джерело лише віддає числа.
 */
interface SiteAnalyticsSource
{
    /** Вигадані цифри: дашборд позначає блок бейджем «Демо-дані». */
    public function isDemo(): bool;

    /** Чи можна зараз брати дані (для GA4 — є доступ до ресурсу). Ні — на дашборді картка «не підключено». */
    public function isAvailable(): bool;

    /**
     * Підсумки за поточний і попередній періоди. Назви подій — як у lib/analytics.ts сайту (SiteAnalytics::EVENTS).
     *
     * @return array{
     *     totals: array{current: array{users: int, sessions: int}, previous: array{users: int, sessions: int}},
     *     events: array{current: array<string, int>, previous: array<string, int>},
     * }
     *
     * @throws \Throwable якщо джерело не відповіло — дашборд покаже картку помилки
     */
    public function fetch(AnalyticsPeriod $period): array;
}
