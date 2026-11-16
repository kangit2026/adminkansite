<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Адмінка') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        :root {
            --kan-navy: #120f82;
            --kan-navy-hover: #1a1599;
            --kan-navy-soft: #ecebfa;
            --kan-ink: #12141c;
            --kan-muted: #5c6370;
            --kan-muted-soft: #8b92a0;
            --kan-bg: #e8eaef;
            --kan-bg-subtle: #f4f5f8;
            --kan-surface: #ffffff;
            --kan-border: #d5d9e2;
            --kan-border-strong: #c2c8d4;
            --kan-hover-bg: #eef0f5;
            --kan-radius: 12px;
            --kan-radius-lg: 16px;
            --kan-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
            --kan-shadow: 0 1px 3px rgba(15, 23, 42, .08), 0 8px 24px -8px rgba(15, 23, 42, .12);
            --kan-sidebar-bg: #fafbfc;
        }

        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        body {
            background-color: var(--kan-bg);
            color: var(--kan-ink);
            font-size: .9375rem;
            font-weight: 400;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ---------- Sidebar ---------- */
        .admin-sidebar {
            min-height: 100vh;
            background: var(--kan-sidebar-bg);
            border-right: 1px solid var(--kan-border-strong);
            box-shadow: 4px 0 24px -12px rgba(15, 23, 42, .08);
            flex-shrink: 0;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            color: var(--kan-ink);
            font-weight: 700;
            font-size: .95rem;
            letter-spacing: -.02em;
            padding: 4px 2px 12px;
            margin-bottom: 4px;
            border-bottom: 1px solid var(--kan-border);
        }

        .admin-brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(145deg, var(--kan-navy) 0%, #2d2a8c 100%);
            box-shadow: 0 4px 12px rgba(18, 15, 130, .35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .72rem;
            color: #fff;
            flex-shrink: 0;
        }

        .admin-nav-section {
            text-transform: uppercase;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .08em;
            color: var(--kan-muted-soft);
            margin: 18px 8px 6px;
            padding-top: 4px;
        }

        .admin-nav-section:not(:first-of-type) {
            margin-top: 22px;
            padding-top: 14px;
            border-top: 1px solid var(--kan-border);
        }

        .admin-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--kan-muted);
            font-size: .875rem;
            font-weight: 500;
            padding: 8px 12px;
            margin-bottom: 2px;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: background-color 140ms ease, color 140ms ease, border-color 140ms ease;
        }

        .admin-sidebar .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            opacity: .9;
        }

        .admin-sidebar .nav-link:hover {
            color: var(--kan-ink);
            background: var(--kan-surface);
            border-color: var(--kan-border);
            box-shadow: var(--kan-shadow-sm);
        }

        .admin-sidebar .nav-link.active {
            color: #fff;
            background: var(--kan-navy);
            border-color: var(--kan-navy);
            box-shadow: 0 4px 14px rgba(18, 15, 130, .28);
        }

        .admin-sidebar .nav-link.active i { opacity: 1; }

        /* ---------- Header ---------- */
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--kan-border-strong);
            box-shadow: var(--kan-shadow-sm);
        }

        .admin-header .page-title {
            font-weight: 700;
            font-size: 1.0625rem;
            letter-spacing: -.02em;
            color: var(--kan-ink);
        }

        .admin-user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px 5px 5px;
            border-radius: 999px;
            background: var(--kan-surface);
            border: 1px solid var(--kan-border);
            box-shadow: var(--kan-shadow-sm);
        }

        .admin-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--kan-navy);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
        }

        /* ---------- Main content ---------- */
        .admin-main {
            min-height: 100vh;
            background:
                radial-gradient(ellipse 120% 80% at 100% -20%, rgba(18, 15, 130, .06), transparent 50%),
                var(--kan-bg);
        }

        .admin-main > main {
            max-width: 1200px;
        }

        main h1.h3, main h1.h4 {
            font-weight: 700;
            letter-spacing: -.025em;
            color: var(--kan-ink);
        }

        main .text-muted { color: var(--kan-muted) !important; }

        /* ---------- Cards / surfaces ---------- */
        .card {
            border: 1px solid var(--kan-border-strong);
            border-radius: var(--kan-radius-lg);
            box-shadow: var(--kan-shadow);
            background: var(--kan-surface);
            overflow: hidden;
        }

        .card + .card { margin-top: 0; }

        .card-header {
            background: var(--kan-surface);
            border-bottom: 1px solid var(--kan-border);
            border-radius: 0 !important;
            font-size: .8125rem;
            font-weight: 600;
            letter-spacing: .01em;
            text-transform: none;
            color: var(--kan-ink);
            padding: .95rem 1.25rem;
        }

        .card-body { padding: 1.25rem 1.35rem; }

        /* ---------- Buttons ---------- */
        .btn { border-radius: 9px; font-weight: 500; font-size: .875rem; }

        .btn-dark, .btn-dark:hover {
            background-color: var(--kan-navy);
            border-color: var(--kan-navy);
        }

        .btn-dark:hover { background-color: var(--kan-navy-hover); border-color: var(--kan-navy-hover); }

        .btn-outline-secondary { border-color: var(--kan-border); color: var(--kan-muted); }
        .btn-outline-secondary:hover { background-color: var(--kan-hover-bg); border-color: var(--kan-border); color: var(--kan-ink); }

        /* ---------- Forms ---------- */
        .form-control, .form-select {
            border-color: var(--kan-border);
            border-radius: 9px;
            font-size: .875rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--kan-navy);
            box-shadow: 0 0 0 .2rem var(--kan-navy-soft);
        }

        .form-label {
            font-weight: 600;
            font-size: .8125rem;
            color: var(--kan-ink);
            margin-bottom: .4rem;
        }

        .form-text { font-size: .78rem; color: var(--kan-muted); }

        .form-control, .form-select {
            background-color: var(--kan-surface);
            box-shadow: var(--kan-shadow-sm);
        }

        .form-control:hover:not(:focus), .form-select:hover:not(:focus) {
            border-color: var(--kan-border-strong);
        }

        /* ---------- Tabs / pills ---------- */
        .nav-tabs {
            border-bottom: 2px solid var(--kan-border);
            gap: 4px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--kan-muted);
            font-weight: 600;
            font-size: .8125rem;
            padding: .55rem 1rem;
            border-radius: 8px 8px 0 0;
            margin-bottom: -2px;
        }

        .nav-tabs .nav-link.active {
            color: var(--kan-navy);
            background: var(--kan-navy-soft);
            border-bottom: 2px solid var(--kan-navy);
        }

        .nav-pills {
            background: var(--kan-bg-subtle);
            border: 1px solid var(--kan-border);
            border-radius: 10px;
            padding: 4px;
            display: inline-flex;
            flex-wrap: wrap;
            gap: 2px;
        }

        .nav-pills .nav-item { margin: 0; }

        .nav-pills .nav-link {
            color: var(--kan-muted);
            font-weight: 600;
            font-size: .8125rem;
            border-radius: 7px;
            padding: .45rem 1rem;
            border: 1px solid transparent;
        }

        .nav-pills .nav-link:hover:not(.active) {
            color: var(--kan-ink);
            background: var(--kan-surface);
        }

        .nav-pills .nav-link.active {
            background: var(--kan-surface);
            color: var(--kan-navy);
            border-color: var(--kan-border);
            box-shadow: var(--kan-shadow-sm);
        }

        /* ---------- Tables ---------- */
        .table { margin-bottom: 0; font-size: .875rem; }
        .table thead th {
            text-transform: uppercase;
            font-size: .65rem;
            letter-spacing: .06em;
            font-weight: 700;
            color: var(--kan-muted);
            background: var(--kan-bg-subtle);
            border-bottom: 1px solid var(--kan-border-strong);
            padding: .85rem 1.1rem;
        }
        .table td {
            padding: .75rem 1.1rem;
            vertical-align: middle;
            border-color: var(--kan-border);
            color: var(--kan-ink);
        }
        .table tbody tr:nth-child(even) { background: rgba(244, 245, 248, .6); }
        .table-hover tbody tr:hover { background-color: var(--kan-navy-soft) !important; }

        .card > .table { border-top: none; }

        /* ---------- Badges ---------- */
        .badge { font-weight: 500; font-size: .72rem; padding: .35em .6em; border-radius: 6px; }

        .badge-accent { background-color: var(--kan-navy-soft); color: var(--kan-navy); }

        /* Перекриваємо системний Bootstrap-акцент (синій/бірюзовий за
           замовчуванням), щоб будь-який невитрачений компонент (пагінація,
           фокус-ринги, чекбокси) теж використовував колір теми. */
        [data-bs-theme=light], :root {
            --bs-primary: var(--kan-navy);
            --bs-primary-rgb: 18, 15, 130;
            --bs-info: var(--kan-navy);
            --bs-info-rgb: 18, 15, 130;
            --bs-link-color: var(--kan-navy);
            --bs-link-color-rgb: 18, 15, 130;
            --bs-link-hover-color: var(--kan-navy-hover);
            --bs-focus-ring-color: rgba(18, 15, 130, .22);
        }

        .alert {
            border-radius: var(--kan-radius);
            border-width: 1px;
            font-size: .875rem;
        }

        .alert-success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .alert-secondary {
            background: var(--kan-bg-subtle);
            border-color: var(--kan-border);
            color: var(--kan-muted);
        }

        .form-check-input:checked {
            background-color: var(--kan-navy);
            border-color: var(--kan-navy);
        }

        .form-check-input:focus { box-shadow: 0 0 0 .2rem var(--kan-navy-soft); }

        .page-link { color: var(--kan-navy); }
        .page-item.active .page-link { background-color: var(--kan-navy); border-color: var(--kan-navy); }
        .page-link:focus { box-shadow: 0 0 0 .2rem var(--kan-navy-soft); }

        /* ---------- Stat cards (dashboard) ---------- */
        .stat-card {
            border: 1px solid var(--kan-border-strong);
            border-radius: var(--kan-radius-lg);
            background: var(--kan-surface);
            padding: 1.15rem 1.35rem;
            box-shadow: var(--kan-shadow);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--kan-navy-soft);
            color: var(--kan-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            border: 1px solid rgba(18, 15, 130, .12);
        }

        .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1.1; color: var(--kan-ink); letter-spacing: -.03em; }
        .stat-label { font-size: .8rem; color: var(--kan-muted); margin-top: 4px; font-weight: 500; }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 1rem 1.15rem;
            border: 1px solid var(--kan-border-strong);
            border-radius: var(--kan-radius-lg);
            background: var(--kan-surface);
            color: var(--kan-ink);
            text-decoration: none;
            box-shadow: var(--kan-shadow-sm);
            transition: border-color 140ms ease, box-shadow 140ms ease, transform 140ms ease;
        }

        .quick-link:hover {
            border-color: var(--kan-navy);
            box-shadow: var(--kan-shadow);
            color: var(--kan-ink);
            transform: translateY(-1px);
        }

        .quick-link .stat-icon { background: var(--kan-bg-subtle); color: var(--kan-muted); }
        .quick-link:hover .stat-icon { background: var(--kan-navy); color: #fff; border-color: transparent; }

        .admin-sidebar-footer .nav-link {
            font-size: .8125rem;
            font-weight: 500;
            padding-left: 0;
        }

        /* ---------- HR: статуси вакансій і кандидатів ---------- */
        .hr-pill {
            display: inline-block;
            padding: .3em .65em;
            border-radius: 6px;
            font-size: .75rem;
            font-weight: 600;
            line-height: 1.3;
            white-space: nowrap;
        }
        .hr-pill-new { background: #ecebfa; color: #120f82; }
        .hr-pill-in_progress { background: #fff1db; color: #7a4300; }
        .hr-pill-interview { background: #e3eefc; color: #0b4a9e; }
        .hr-pill-reserve, .hr-pill-draft { background: #eef0f3; color: #454c59; }
        .hr-pill-rejected { background: #fde8e8; color: #9b1c1c; }
        .hr-pill-hired, .hr-pill-active { background: #e2f4ea; color: #11643a; }
        .hr-pill-closed { background: #e4e6eb; color: #33394a; }
        .hr-row-new { --bs-table-bg: #f7f7fd; }
        .hr-row-new .hr-name { font-weight: 700; }

        .admin-sidebar .nav-link .hr-count {
            margin-left: auto;
            min-width: 22px;
            padding: 1px 7px;
            border-radius: 999px;
            background: var(--kan-navy-soft);
            color: var(--kan-navy);
            font-size: .72rem;
            font-weight: 700;
            text-align: center;
        }
        .admin-sidebar .nav-link.active .hr-count { background: #fff; }

        .hr-note-system { color: var(--kan-muted); font-style: italic; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
@auth
    <div class="d-flex">
        <nav class="admin-sidebar p-3 d-flex flex-column" style="width: 264px;">
            <div class="admin-brand px-2 py-2 mb-2">
                <div class="admin-brand-mark">KAN</div>
                <span>Admin</span>
            </div>

            @role('admin')
            <div class="admin-nav-section">Загальне</div>
            <div class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Дашборд</a>
                <a class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}" href="{{ route('admin.statistics') }}"><i class="bi bi-bar-chart-line"></i> Статистика</a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-person-gear"></i> Користувачі</a>
            </div>
            @endrole

            <div class="admin-nav-section">HR</div>
            <div class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.vacancies.*') ? 'active' : '' }}" href="{{ route('admin.vacancies.index') }}"><i class="bi bi-briefcase"></i> Вакансії</a>
                <a class="nav-link {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}" href="{{ route('admin.candidates.index') }}">
                    <i class="bi bi-people"></i> Кандидати
                    @if ($hrNewCandidates > 0)
                        <span class="hr-count" title="Нових відгуків">{{ $hrNewCandidates }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('admin.hr-settings.*') ? 'active' : '' }}" href="{{ route('admin.hr-settings.edit') }}"><i class="bi bi-sliders"></i> Налаштування HR</a>
            </div>

            @role('admin')
            <div class="admin-nav-section">Контент</div>
            <div class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><i class="bi bi-house"></i> Головна</a>
                <a class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}"><i class="bi bi-newspaper"></i> Новини</a>
                <a class="nav-link {{ request()->routeIs('admin.news-tags.*') ? 'active' : '' }}" href="{{ route('admin.news-tags.index') }}"><i class="bi bi-tags"></i> Теги новин</a>
                <a class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" href="{{ route('admin.projects.index') }}"><i class="bi bi-buildings"></i> Проєкти</a>
                <a class="nav-link {{ request()->routeIs('admin.project-categories.*') ? 'active' : '' }}" href="{{ route('admin.project-categories.index') }}"><i class="bi bi-diagram-3"></i> Категорії проєктів</a>
                <a class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.edit') }}"><i class="bi bi-envelope"></i> Контакти</a>
                <a class="nav-link {{ request()->routeIs('admin.footer.*') ? 'active' : '' }}" href="{{ route('admin.footer.edit') }}"><i class="bi bi-layout-text-window-reverse"></i> Футер</a>
                <a class="nav-link {{ request()->routeIs('admin.contact-submissions.*') ? 'active' : '' }}" href="{{ route('admin.contact-submissions.index') }}"><i class="bi bi-inbox"></i> Заявки з форми</a>
            </div>
            @endrole

            <div class="mt-auto px-2 pt-3 admin-sidebar-footer border-top" style="border-color: var(--kan-border-strong) !important;">
                <a href="{{ url('/') }}" target="_blank" class="nav-link p-0 pt-3">
                    <i class="bi bi-box-arrow-up-right"></i> Переглянути сайт
                </a>
            </div>
        </nav>
        <div class="flex-grow-1 admin-main">
            <header class="admin-header d-flex justify-content-between align-items-center px-4 py-3">
                <div class="page-title">@yield('title', 'Адмінка')</div>
                <div class="position-relative" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false">
                    <button type="button" class="admin-user-chip" style="cursor:pointer;" x-on:click="open = ! open" aria-haspopup="true" :aria-expanded="open">
                        <div class="admin-user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                        <span class="small pe-1 text-start" style="line-height:1.2;">
                            <span class="d-block fw-semibold">{{ auth()->user()->name }}</span>
                            <span class="d-block text-muted" style="font-size:.7rem;">{{ auth()->user()->hasRole('admin') ? 'Адміністратор' : (auth()->user()->hasRole('hr') ? 'HR' : '') }}</span>
                        </span>
                        <i class="bi bi-chevron-down small text-muted pe-1"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition.opacity class="dropdown-menu show end-0 mt-2 p-2 shadow" style="min-width:240px;">
                        <div class="px-2 pb-2 border-bottom mb-2">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <div class="small text-muted text-break">{{ auth()->user()->email }}</div>
                        </div>
                        @role('admin')
                            <a class="dropdown-item rounded" href="{{ route('admin.users.index') }}"><i class="bi bi-person-gear me-2"></i>Користувачі та імена</a>
                        @endrole
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item rounded"><i class="bi bi-box-arrow-right me-2"></i>Вийти</button>
                        </form>
                    </div>
                </div>
            </header>
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>
@else
    @yield('content')
@endauth
@stack('admin-scripts')
</body>
</html>
