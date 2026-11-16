{{--
  Сторінка «Статистика» (меню «Загальне»), три вкладки: відвідуваність сайту (джерело: демо або GA4), заявки та відгуки
  і поширення новин (обидві — справжні, з бази адмінки). Дані — SiteAnalytics::report(). Відкрита вкладка — у #адресі.
--}}
@extends('admin.layout')

@section('title', 'Статистика')

@section('content')
@php
    $traffic = $analytics['traffic'];
    $site = $analytics['site'];
    $num = fn ($n) => number_format((int) $n, 0, ',', "\u{202F}"); // 1 240 — вузький нерозривний пробіл
    // [css-клас, текст] для порівняння з попереднім періодом
    $trend = function (?int $pct) {
        if ($pct === null) {
            return ['an-flat', '—'];
        }
        if ($pct === 0) {
            return ['an-flat', 'без змін'];
        }

        return $pct > 0 ? ['an-up', '↑ '.$pct.'%'] : ['an-down', '↓ '.abs($pct).'%'];
    };
    $tiles = $traffic['status'] === 'ok' ? $traffic['tiles'] : [];
    $shares = $analytics['shares'];
@endphp

<style>
    [x-cloak] { display: none !important; }
    .an-tabs { margin-bottom: 1.25rem; }
    .an-tabs .nav-link { display: inline-flex; align-items: center; gap: 7px; }
    .an-demo-badge { display: inline-flex; align-items: center; gap: 4px; padding: .1em .5em; border-radius: 6px; background: #fff1db; color: #7a4300; font-size: .7rem; font-weight: 700; line-height: 1.5; }
    .an-demo-note { display: flex; align-items: center; gap: 8px; margin-bottom: 1rem; padding: .6rem .9rem; border: 1px solid #f5d9a8; border-radius: var(--kan-radius); background: #fff8ec; color: #7a4300; font-size: .84rem; }
    .an-tile { height: 100%; padding: 1rem 1.15rem; border: 1px solid var(--kan-border-strong); border-radius: var(--kan-radius-lg); background: var(--kan-surface); box-shadow: var(--kan-shadow-sm); }
    .an-tile-label { display: flex; align-items: center; gap: 7px; font-size: .8rem; font-weight: 500; color: var(--kan-muted); }
    .an-tile-label i { color: var(--kan-navy); }
    .an-tile-value { margin-top: 6px; font-size: 1.6rem; font-weight: 800; line-height: 1.15; letter-spacing: -.03em; color: var(--kan-ink); font-variant-numeric: tabular-nums; }
    .an-tile-sm .an-tile-value { font-size: 1.25rem; }
    .an-change { margin-top: 3px; font-size: .75rem; font-weight: 600; }
    .an-change .an-was { margin-left: 4px; font-weight: 400; color: var(--kan-muted-soft); }
    .an-up { color: #11643a; }
    .an-down { color: #9b1c1c; }
    .an-flat { color: var(--kan-muted-soft); }
    .an-chart { position: relative; height: 260px; }
    .an-note { font-size: .8rem; color: var(--kan-muted); }
    .an-empty { padding: 1.25rem 1.35rem; border: 1px dashed var(--kan-border-strong); border-radius: var(--kan-radius-lg); background: var(--kan-bg-subtle); }
    .an-empty .stat-icon { background: var(--kan-surface); color: var(--kan-muted); border-color: var(--kan-border); }
    .an-types { margin-top: .6rem; font-size: .78rem; color: var(--kan-muted); }
    .an-bars, .an-top { margin: 0; padding: 0; list-style: none; }
    .an-bars { display: grid; gap: .85rem; }
    .an-bar-row { display: grid; grid-template-columns: minmax(0, 15rem) 1fr 6.5rem; align-items: center; gap: .9rem; font-size: .85rem; }
    .an-bar-label { display: flex; align-items: center; gap: 8px; min-width: 0; color: var(--kan-ink); }
    .an-bar-label i { color: var(--kan-navy); }
    .an-bar-track { height: 8px; border-radius: 4px; background: var(--kan-bg-subtle); overflow: hidden; }
    .an-bar-fill { height: 100%; border-radius: 4px; background: #120f82; }
    .an-bar-num { text-align: right; font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .an-bar-num span { margin-left: 4px; font-weight: 400; color: var(--kan-muted-soft); }
    .an-top li { display: flex; align-items: baseline; gap: .75rem; padding: .6rem 0; border-top: 1px solid var(--kan-border); font-size: .88rem; }
    .an-top li:first-child { border-top: 0; padding-top: 0; }
    .an-top a { flex: 1; min-width: 0; color: var(--kan-ink); text-decoration: none; }
    .an-top a:hover { color: var(--kan-navy); text-decoration: underline; }
    .an-top-rank { width: 1.4rem; color: var(--kan-muted-soft); font-variant-numeric: tabular-nums; }
    .an-top-num { font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }
    @media (max-width: 575.98px) {
        .an-bar-row { grid-template-columns: 1fr auto; gap: .35rem .75rem; }
        .an-bar-track { grid-column: 1 / -1; order: 3; }
    }
</style>

<h1 class="h3 mb-1">Статистика сайту</h1>
<p class="text-muted mb-4">Останні {{ $analytics['period']['days'] }} днів · {{ $analytics['period']['label'] }} · порівняно з попередніми {{ $analytics['period']['days'] }} днями</p>

<div x-data="{
        tab: ['traffic', 'leads', 'shares'].includes(location.hash.slice(1)) ? location.hash.slice(1) : 'traffic',
        open(name) { this.tab = name; history.replaceState(null, '', '#' + name); },
    }">
    <ul class="nav nav-pills an-tabs" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" :class="tab === 'traffic' && 'active'" :aria-selected="tab === 'traffic'" @click="open('traffic')">
                <i class="bi bi-graph-up-arrow"></i> Відвідуваність сайту
                @if ($traffic['demo'])
                    <span class="an-demo-badge" data-analytics-demo><i class="bi bi-cone-striped"></i> Демо-дані</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" :class="tab === 'leads' && 'active'" :aria-selected="tab === 'leads'" @click="open('leads')">
                <i class="bi bi-inbox"></i> Заявки та відгуки
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" :class="tab === 'shares' && 'active'" :aria-selected="tab === 'shares'" @click="open('shares')">
                <i class="bi bi-share"></i> Поширення новин
            </button>
        </li>
    </ul>

    {{-- ---------- Відвідуваність сайту ---------- --}}
    <div role="tabpanel" x-show="tab === 'traffic'" data-tab="traffic">
        @if ($traffic['status'] === 'ok')
            @if ($traffic['demo'])
                <div class="an-demo-note"><i class="bi bi-info-circle"></i> Цифри нижче вигадані для прикладу. Реальні дані Google Analytics підключимо під час запуску сайту.</div>
            @endif

            <div class="row g-3 mb-3">
                @foreach ($tiles as $i => $tile)
                    @php [$trendClass, $trendText] = $trend($tile['change']); @endphp
                    {{-- перші чотири — головні, кліки на контакти — дрібніше, рядком нижче --}}
                    <div class="{{ $i < 4 ? 'col-6 col-xl-3' : 'col-12 col-md-4' }}">
                        <div class="an-tile {{ $i < 4 ? '' : 'an-tile-sm' }}" data-metric="{{ $tile['key'] }}">
                            <div class="an-tile-label"><i class="bi {{ $tile['icon'] }}"></i> {{ $tile['label'] }}</div>
                            <div class="an-tile-value">{{ $num($tile['current']) }}</div>
                            <div class="an-change {{ $trendClass }}">{{ $trendText }}<span class="an-was">було {{ $num($tile['previous']) }}</span></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="an-note mb-0"><i class="bi bi-info-circle"></i>
                Google Analytics рахує лише відвідувачів, які погодилися на cookies, тож реальний трафік і кількість заявок будуть більші за його цифри.
                Точна кількість заявок — на вкладці «Заявки та відгуки».
                @isset($traffic['fetchedAt'])
                    Дані GA оновлено о {{ $traffic['fetchedAt'] }}, оновлюються раз на годину.
                @endisset
            </p>
        @else
            <div class="an-empty" data-ga-status="{{ $traffic['status'] }}">
                <div class="d-flex gap-3">
                    <div class="stat-icon"><i class="bi bi-bar-chart-line"></i></div>
                    <div>
                        <div class="fw-semibold">Google Analytics не підключено</div>
                        @if ($traffic['status'] === 'error')
                            <div class="an-note">Не вдалося отримати дані з Google Analytics. Наступна спроба — за кілька хвилин.</div>
                        @else
                            <div class="an-note">Реальні дані Google Analytics підключимо під час запуску сайту. Тут з’являться відвідувачі та події сайту.</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ---------- Заявки та відгуки: база адмінки, працює без GA ---------- --}}
    <div role="tabpanel" x-show="tab === 'leads'" x-cloak data-tab="leads">
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-3 h-100">
                    @php [$trendClass, $trendText] = $trend($site['submissions']['change']); @endphp
                    <div class="an-tile" data-metric="db-submissions">
                        <div class="an-tile-label"><i class="bi bi-inbox"></i> Заявки з форм сайту</div>
                        <div class="an-tile-value">{{ $num($site['submissions']['current']) }}</div>
                        <div class="an-change {{ $trendClass }}">{{ $trendText }}<span class="an-was">було {{ $num($site['submissions']['previous']) }}</span></div>
                        @if ($site['submissions']['byType'])
                            <div class="an-types">
                                @foreach ($site['submissions']['byType'] as $label => $count)
                                    {{ $label }} — {{ $num($count) }}@if (! $loop->last) · @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @if ($site['candidates'] !== null)
                        @php [$trendClass, $trendText] = $trend($site['candidates']['change']); @endphp
                        <div class="an-tile" data-metric="db-candidates">
                            <div class="an-tile-label"><i class="bi bi-person-badge"></i> Відгуки кандидатів</div>
                            <div class="an-tile-value">{{ $num($site['candidates']['current']) }}</div>
                            <div class="an-change {{ $trendClass }}">{{ $trendText }}<span class="an-was">було {{ $num($site['candidates']['previous']) }}</span></div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">По тижнях <span class="text-muted fw-normal">· останні {{ count($site['weeks']['labels']) }}</span></div>
                    <div class="card-body">
                        <div class="an-chart">
                            <canvas id="an-weekly" role="img" aria-label="Заявки{{ $site['candidates'] !== null ? ' і відгуки кандидатів' : '' }} по тижнях"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p class="an-note mt-3 mb-0"><i class="bi bi-database-check"></i> Повний облік з бази адмінки — не залежить від cookies і Google Analytics.</p>
    </div>

    {{-- ---------- Поширення новин: натискання «Поділитися» на сторінці новини, база адмінки ---------- --}}
    <div role="tabpanel" x-show="tab === 'shares'" x-cloak data-tab="shares">
        @if ($shares['current'] === 0 && $shares['previous'] === 0)
            <div class="an-empty" data-shares-empty>
                <div class="d-flex gap-3">
                    <div class="stat-icon"><i class="bi bi-share"></i></div>
                    <div>
                        <div class="fw-semibold">За цей період новинами ще не ділилися</div>
                        <div class="an-note">Тут з’явиться, скільки разів відвідувачі натиснули «Поділитися» на сторінці новини, через які канали і якими новинами діляться найчастіше.</div>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-3 h-100">
                        @php [$trendClass, $trendText] = $trend($shares['change']); @endphp
                        <div class="an-tile" data-metric="shares-total">
                            <div class="an-tile-label"><i class="bi bi-share"></i> Поширень новин</div>
                            <div class="an-tile-value">{{ $num($shares['current']) }}</div>
                            <div class="an-change {{ $trendClass }}">{{ $trendText }}<span class="an-was">було {{ $num($shares['previous']) }}</span></div>
                        </div>
                        <div class="an-tile" data-metric="shares-news">
                            <div class="an-tile-label"><i class="bi bi-newspaper"></i> Новин, якими поділилися</div>
                            <div class="an-tile-value">{{ $num($shares['news']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">За каналами</div>
                        <div class="card-body">
                            <ul class="an-bars">
                                @foreach ($shares['channels'] as $channel)
                                    <li class="an-bar-row" data-channel="{{ $channel['key'] }}">
                                        <span class="an-bar-label"><i class="bi {{ $channel['icon'] }}"></i> {{ $channel['label'] }}</span>
                                        <span class="an-bar-track" aria-hidden="true"><span class="an-bar-fill d-block" style="width: {{ $channel['bar'] }}%"></span></span>
                                        <span class="an-bar-num">{{ $num($channel['count']) }}<span>{{ $channel['percent'] }}%</span></span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @if ($shares['top'])
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">Найчастіше поширювали</div>
                            <div class="card-body">
                                <ol class="an-top" data-top-news>
                                    @foreach ($shares['top'] as $item)
                                        <li>
                                            <span class="an-top-rank">{{ $loop->iteration }}</span>
                                            <a href="{{ route('admin.news.edit', $item['id']) }}">{{ $item['title'] }}</a>
                                            <span class="an-top-num">{{ $num($item['count']) }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        <p class="an-note mt-3 mb-0"><i class="bi bi-info-circle"></i>
            Рахуємо натискання кнопок у блоці «Поділитися новиною» на сайті, а не самі дописи: соцмережі не повідомляють, чи людина зрештою поділилася.
            Дані з бази адмінки, без персональних даних.
        </p>
    </div>
</div>
@endsection

@push('admin-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const el = document.getElementById('an-weekly');
            if (!window.Chart || !el) return;

            Chart.defaults.font.family = "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#8b92a0';

            const weeks = @json($site['weeks']);
            const datasets = [{ label: 'Заявки з форм', data: weeks.submissions, backgroundColor: '#120f82', borderRadius: 4, maxBarThickness: 28 }];
            if (weeks.candidates) {
                datasets.push({ label: 'Відгуки кандидатів', data: weeks.candidates, backgroundColor: '#9b98e3', borderRadius: 4, maxBarThickness: 28 });
            }

            // вкладка може бути прихована (x-show) — Chart.js перемалює графік, коли контейнер отримає розмір
            new Chart(el, {
                type: 'bar',
                data: { labels: weeks.labels, datasets },
                options: {
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', align: 'end', labels: { boxWidth: 10, boxHeight: 10, useBorderRadius: true, borderRadius: 2 } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 12 } },
                        y: { beginAtZero: true, grid: { color: '#e4e6eb' }, border: { display: false }, ticks: { precision: 0 } },
                    },
                },
            });
        })();
    </script>
@endpush
