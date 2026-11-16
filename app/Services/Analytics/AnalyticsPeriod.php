<?php

namespace App\Services\Analytics;

use Carbon\CarbonImmutable;

/** Поточний період і попередній такої ж довжини. Кінці не включно: [from, to). */
final readonly class AnalyticsPeriod
{
    public function __construct(
        public CarbonImmutable $from,
        public CarbonImmutable $to,
        public CarbonImmutable $previousFrom,
        public CarbonImmutable $previousTo,
    ) {}

    /** Останні $days повних днів до $today (сьогодні не рахуємо — день ще триває) і стільки ж перед ними. */
    public static function lastDays(int $days, CarbonImmutable $today): self
    {
        $today = $today->startOfDay();

        return new self($today->subDays($days), $today, $today->subDays($days * 2), $today->subDays($days));
    }

    /** @return list<CarbonImmutable> */
    public function days(): array
    {
        return self::range($this->from, $this->to);
    }

    /** @return list<CarbonImmutable> */
    public function previousDays(): array
    {
        return self::range($this->previousFrom, $this->previousTo);
    }

    /** @return list<CarbonImmutable> */
    private static function range(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $days = [];
        for ($day = $from; $day < $to; $day = $day->addDay()) {
            $days[] = $day;
        }

        return $days;
    }
}
