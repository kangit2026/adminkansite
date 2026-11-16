<?php

namespace App\Services\Analytics;

use Carbon\CarbonImmutable;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Правдоподібні демо-цифри відвідуваності, поки реальний GA4 не підключено.
 * Кожен день генерується з власного зерна (дата), тож цифри не стрибають між оновленнями сторінки,
 * а від дня до дня змінюються плавно, як справжні.
 */
class DemoAnalyticsSource implements SiteAnalyticsSource
{
    /** Від цієї дати рахується повільний ріст трафіку. */
    private const EPOCH = '2026-01-01';

    /** Частка денних користувачів, що роблять подію. */
    private const EVENT_RATES = [
        'generate_lead' => 0.009,
        'job_apply' => 0.003,
        'click_phone' => 0.02,
        'click_email' => 0.006,
        'click_messenger' => 0.011,
    ];

    public function isDemo(): bool
    {
        return true;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function fetch(AnalyticsPeriod $period): array
    {
        [$totalsNow, $eventsNow] = $this->sum($period->days());
        [$totalsBefore, $eventsBefore] = $this->sum($period->previousDays());

        return [
            'totals' => ['current' => $totalsNow, 'previous' => $totalsBefore],
            'events' => ['current' => $eventsNow, 'previous' => $eventsBefore],
        ];
    }

    /**
     * Сума днів; унікальних користувачів за період менше, ніж сума денних.
     *
     * @param  list<CarbonImmutable>  $days
     */
    private function sum(array $days): array
    {
        $users = $sessions = 0;
        $events = array_fill_keys(array_keys(self::EVENT_RATES), 0);

        foreach ($days as $day) {
            $one = $this->day($day);
            $users += $one['users'];
            $sessions += $one['sessions'];
            foreach ($one['events'] as $event => $count) {
                $events[$event] += $count;
            }
        }

        return [['users' => (int) round($users * .74), 'sessions' => $sessions], $events];
    }

    /** Один день: будні жвавіші за вихідні, повільний ріст від EPOCH, шум ±15 %. */
    private function day(CarbonImmutable $day): array
    {
        $rng = new Randomizer(new Mt19937(crc32('kan-demo-analytics:day:'.$day->toDateString())));
        $between = fn (float $min, float $max) => $min + ($max - $min) * $rng->getInt(0, 1_000_000) / 1_000_000;

        $growth = 1 + 0.0012 * max(0, (int) CarbonImmutable::parse(self::EPOCH, $day->timezone)->diffInDays($day));
        $users = (int) round(($day->isWeekend() ? 96 : 142) * $growth * $between(.85, 1.15));

        $events = [];
        foreach (self::EVENT_RATES as $event => $rate) {
            $events[$event] = (int) floor($users * $rate + $between(0, 1));
        }

        return [
            'users' => $users,
            'sessions' => (int) round($users * $between(1.22, 1.4)),
            'events' => $events,
        ];
    }
}
