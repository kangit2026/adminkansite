<?php

namespace App\Services;

use App\Models\ContactSubmission;
use App\Models\NewsShare;
use App\Services\Analytics\AnalyticsPeriod;
use App\Services\Analytics\DemoAnalyticsSource;
use App\Services\Analytics\Ga4AnalyticsSource;
use App\Services\Analytics\SiteAnalyticsSource;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Сторінка «Статистика»: останні 30 днів проти попередніх 30.
 * Відвідуваність і події сайту — з джерела (config analytics.source: demo зараз, ga4 — після запуску);
 * заявки, відгуки й поширення новин — завжди справжні, з бази адмінки.
 */
class SiteAnalytics
{
    public const DAYS = 30;

    /** Скільки календарних тижнів (з поточним) показувати в розбивці з бази. */
    public const WEEKS = 5;

    /** Скільки новин показувати в «Найчастіше поширювали». */
    public const TOP_NEWS = 5;

    /** analytics.source → клас джерела. */
    public const SOURCES = [
        'demo' => DemoAnalyticsSource::class,
        'ga4' => Ga4AnalyticsSource::class,
    ];

    /** Події сайту (lib/analytics.ts у kan-site-v2) → підписи плиток. */
    public const EVENTS = [
        'generate_lead' => ['label' => 'Заявки з форм', 'icon' => 'bi-send-check'],
        'job_apply' => ['label' => 'Відгуки на вакансії', 'icon' => 'bi-person-badge'],
        'click_phone' => ['label' => 'Кліки на телефон', 'icon' => 'bi-telephone'],
        'click_email' => ['label' => 'Кліки на e-mail', 'icon' => 'bi-envelope'],
        'click_messenger' => ['label' => 'Кліки на месенджери', 'icon' => 'bi-chat-dots'],
    ];

    private const MONTHS = ['січ.', 'лют.', 'бер.', 'квіт.', 'трав.', 'черв.', 'лип.', 'серп.', 'вер.', 'жовт.', 'лист.', 'груд.'];

    /** Джерело підставляє контейнер, якщо його явно зв’язано (тести); інакше — за config('analytics.source'). */
    public function __construct(private ?SiteAnalyticsSource $source = null) {}

    public function report(): array
    {
        $today = CarbonImmutable::now(config('analytics.timezone'))->startOfDay();
        $period = AnalyticsPeriod::lastDays(self::DAYS, $today);

        return [
            'period' => [
                'label' => $this->rangeLabel($period->from, $period->to->subDay(), withYear: true),
                'days' => self::DAYS,
            ],
            'traffic' => $this->traffic($period),
            'site' => $this->siteData($period, $today),
            'shares' => $this->shares($period),
        ];
    }

    public static function change(int|float $current, int|float $previous): ?int
    {
        if ($previous <= 0) {
            return null;
        }

        return (int) round(($current - $previous) / $previous * 100);
    }

    private function source(): SiteAnalyticsSource
    {
        return $this->source ??= app(self::SOURCES[config('analytics.source')] ?? DemoAnalyticsSource::class);
    }

    // ---------- Відвідуваність (джерело) ----------

    private function traffic(AnalyticsPeriod $period): array
    {
        $source = $this->source();

        if (! $source->isAvailable()) {
            return ['status' => 'not_configured', 'demo' => false];
        }

        if ($source->isDemo()) {
            return ['status' => 'ok', 'demo' => true] + $this->present($source->fetch($period));
        }

        // справжнє джерело: годину з кешу, після помилки — пауза, щоб не смикати API на кожному відкритті
        $key = 'site-analytics:'.class_basename($source).':'.$period->from->toDateString();
        if (Cache::has($key.':error')) {
            return ['status' => 'error', 'demo' => false];
        }

        try {
            $data = Cache::remember($key, (int) config('analytics.cache_ttl', 3600), fn () => $source->fetch($period) + [
                'fetchedAt' => CarbonImmutable::now(config('analytics.timezone'))->format('H:i'),
            ]);
        } catch (Throwable $e) {
            Log::warning('Site analytics source failed: '.$e->getMessage());
            Cache::put($key.':error', true, (int) config('analytics.error_ttl', 600));

            return ['status' => 'error', 'demo' => false];
        }

        return ['status' => 'ok', 'demo' => false, 'fetchedAt' => $data['fetchedAt']] + $this->present($data);
    }

    /** Числа джерела → плитки з порівнянням із попереднім періодом. */
    private function present(array $data): array
    {
        $totals = $data['totals'];
        $events = $data['events'];

        $tiles = [
            $this->tile('users', 'Користувачі', 'bi-people', $totals['current']['users'] ?? 0, $totals['previous']['users'] ?? 0),
            $this->tile('sessions', 'Сесії', 'bi-window-stack', $totals['current']['sessions'] ?? 0, $totals['previous']['sessions'] ?? 0),
        ];
        foreach (self::EVENTS as $event => $meta) {
            $tiles[] = $this->tile($event, $meta['label'], $meta['icon'], $events['current'][$event] ?? 0, $events['previous'][$event] ?? 0);
        }

        return ['tiles' => $tiles];
    }

    private function tile(string $key, string $label, string $icon, int|float $current, int|float $previous): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'icon' => $icon,
            'current' => (int) $current,
            'previous' => (int) $previous,
            'change' => self::change($current, $previous),
        ];
    }

    // ---------- База адмінки (справжні дані) ----------

    private function siteData(AnalyticsPeriod $period, CarbonImmutable $today): array
    {
        $weekStarts = [];
        $thisWeek = $today->startOfWeek(CarbonInterface::MONDAY);
        for ($i = self::WEEKS - 1; $i >= 0; $i--) {
            $weekStarts[] = $thisWeek->subWeeks($i);
        }
        $since = $period->previousFrom->min($weekStarts[0]);

        $submissions = $this->timestamps('contact_submissions', $since, withType: true);
        // таблиця кандидатів з’являється разом із HR-розділом
        $candidates = Schema::hasTable('candidates') ? $this->timestamps('candidates', $since) : null;

        $byType = [];
        foreach ($submissions as [$at, $type]) {
            if ($this->within($at, $period->from, $period->to)) {
                $label = ContactSubmission::TYPES[$type] ?? $type;
                $byType[$label] = ($byType[$label] ?? 0) + 1;
            }
        }
        arsort($byType);

        $weeks = ['labels' => [], 'submissions' => [], 'candidates' => $candidates === null ? null : []];
        foreach ($weekStarts as $start) {
            $end = $start->addWeek();
            $weeks['labels'][] = $start->equalTo($thisWeek) ? 'Цей тиждень' : $this->rangeLabel($start, $end->subDay());
            $weeks['submissions'][] = $this->countWithin($submissions, $start, $end);
            if ($candidates !== null) {
                $weeks['candidates'][] = $this->countWithin($candidates, $start, $end);
            }
        }

        return [
            'submissions' => $this->periodCounts($submissions, $period) + ['byType' => $byType],
            'candidates' => $candidates === null ? null : $this->periodCounts($candidates, $period),
            'weeks' => $weeks,
        ];
    }

    /** @return list<array{0: CarbonImmutable, 1: ?string}> час створення (у поясі аналітики) і тип заявки */
    private function timestamps(string $table, CarbonImmutable $since, bool $withType = false): array
    {
        $appTz = config('app.timezone');
        $tz = config('analytics.timezone');

        return DB::table($table)
            ->where('created_at', '>=', $since->setTimezone($appTz)->format('Y-m-d H:i:s'))
            ->get($withType ? ['created_at', 'type'] : ['created_at'])
            ->map(fn ($row) => [CarbonImmutable::parse($row->created_at, $appTz)->setTimezone($tz), $row->type ?? null])
            ->all();
    }

    private function periodCounts(array $rows, AnalyticsPeriod $period): array
    {
        $current = $this->countWithin($rows, $period->from, $period->to);
        $previous = $this->countWithin($rows, $period->previousFrom, $period->previousTo);

        return ['current' => $current, 'previous' => $previous, 'change' => self::change($current, $previous)];
    }

    private function countWithin(array $rows, CarbonImmutable $from, CarbonImmutable $to): int
    {
        return count(array_filter($rows, fn ($row) => $this->within($row[0], $from, $to)));
    }

    private function within(CarbonImmutable $at, CarbonImmutable $from, CarbonImmutable $to): bool
    {
        return $at >= $from && $at < $to;
    }

    // ---------- Поширення новин (база адмінки) ----------

    private function shares(AnalyticsPeriod $period): array
    {
        $current = fn () => $this->sharesWithin($period->from, $period->to);

        $total = $current()->count();
        $previous = $this->sharesWithin($period->previousFrom, $period->previousTo)->count();
        $byChannel = $current()->selectRaw('channel, count(*) as total')->groupBy('channel')->pluck('total', 'channel');

        $max = (int) $byChannel->max();
        $channels = [];
        foreach (NewsShare::CHANNELS as $key => $meta) {
            $count = (int) ($byChannel[$key] ?? 0);
            $channels[] = $meta + [
                'key' => $key,
                'count' => $count,
                'percent' => $total ? (int) round($count / $total * 100) : 0,
                'bar' => $max ? round($count / $max * 100, 1) : 0, // ширина смужки відносно лідера
            ];
        }
        usort($channels, fn ($a, $b) => $b['count'] <=> $a['count']); // стабільне: за рівних — порядок як на сайті

        $top = $current()
            ->selectRaw('news_id, count(*) as total')
            ->groupBy('news_id')
            ->orderByDesc('total')
            ->orderByDesc('news_id')
            ->limit(self::TOP_NEWS)
            ->with('news:id,title')
            ->get()
            ->map(fn (NewsShare $row) => [
                'id' => $row->news_id,
                'title' => $row->news?->getTranslation('title', 'uk') ?: 'Новина #'.$row->news_id,
                'count' => (int) $row->total,
            ])
            ->all();

        return [
            'current' => $total,
            'previous' => $previous,
            'change' => self::change($total, $previous),
            'news' => $current()->distinct()->count('news_id'),
            'channels' => $channels,
            'top' => $top,
        ];
    }

    private function sharesWithin(CarbonImmutable $from, CarbonImmutable $to)
    {
        $appTz = config('app.timezone');

        return NewsShare::query()
            ->where('created_at', '>=', $from->setTimezone($appTz)->format('Y-m-d H:i:s'))
            ->where('created_at', '<', $to->setTimezone($appTz)->format('Y-m-d H:i:s'));
    }

    /** «3–9 лист.», «27 жовт. – 2 лист.»; з роком — для заголовка періоду. */
    private function rangeLabel(CarbonImmutable $from, CarbonImmutable $to, bool $withYear = false): string
    {
        $year = $withYear ? ' '.$to->year : '';

        if ($from->month === $to->month && $from->year === $to->year) {
            return $from->day.'–'.$to->day.' '.self::MONTHS[$to->month - 1].$year;
        }

        return $from->day.' '.self::MONTHS[$from->month - 1].' – '.$to->day.' '.self::MONTHS[$to->month - 1].$year;
    }
}
