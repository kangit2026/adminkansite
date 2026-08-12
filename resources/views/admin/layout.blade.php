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
            --kan-navy-hover: #1d4ed8;
            --kan-navy-soft: #eff6ff;
            --kan-ink: #1e2130;
            --kan-muted: #6b7280;
            --kan-muted-soft: #9ca3af;
            --kan-bg: #f1f2f5;
            --kan-surface: #ffffff;
            --kan-border: #e2e4ea;
            --kan-hover-bg: #f5f6f9;
            --kan-radius: 14px;
            --kan-shadow: 0 1px 2px rgba(16, 20, 40, .04), 0 10px 24px -12px rgba(16, 20, 40, .12);
        }

        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        body {
            background-color: var(--kan-bg);
            color: var(--kan-ink);
            font-size: .925rem;
            font-weight: 400;
        }

        /* ---------- Sidebar ---------- */
        .admin-sidebar {
            min-height: 100vh;
            background: var(--kan-surface);
            border-right: 1px solid var(--kan-border);
            flex-shrink: 0;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--kan-ink);
            font-weight: 700;
            letter-spacing: .01em;
        }

        .admin-brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--kan-navy),rgb(40 39 41));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .8rem;
            color: #fff;
            flex-shrink: 0;
        }

        .admin-nav-section {
            text-transform: uppercase;
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: var(--kan-muted-soft);
            margin: 20px 10px 8px;
        }

        .admin-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--kan-muted);
            font-size: .875rem;
            font-weight: 500;
            padding: 9px 12px;
            border-radius: 10px;
            transition: background-color 120ms ease, color 120ms ease;
        }

        .admin-sidebar .nav-link i { font-size: 1rem; width: 18px; text-align: center; opacity: .85; }

        .admin-sidebar .nav-link:hover { color: var(--kan-ink); background: var(--kan-hover-bg); }

        .admin-sidebar .nav-link.active {
            color: var(--kan-navy);
            background: var(--kan-navy-soft);
            box-shadow: inset 3px 0 0 0 var(--kan-navy);
        }

        /* ---------- Header ---------- */
        .admin-header {
            background: var(--kan-surface);
            border-bottom: 1px solid var(--kan-border);
            box-shadow: 0 1px 0 rgba(16, 20, 40, .015);
        }

        .admin-header .page-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--kan-ink);
        }

        .admin-user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 6px 4px 4px;
            border-radius: 999px;
            background: var(--kan-hover-bg);
        }

        .admin-user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--kan-navy-soft);
            color: var(--kan-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
        }

        /* ---------- Cards / surfaces ---------- */
        .card {
            border: 1px solid var(--kan-border);
            border-radius: var(--kan-radius);
            box-shadow: var(--kan-shadow);
        }

        .card-header {
            background: var(--kan-hover-bg);
            border-bottom: 1px solid var(--kan-border);
            border-radius: var(--kan-radius) var(--kan-radius) 0 0 !important;
            font-size: .875rem;
            padding: .85rem 1.15rem;
        }

        .card-body { padding: 1.15rem; }

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

        .form-label { font-weight: 500; font-size: .8rem; color: var(--kan-muted); margin-bottom: .35rem; }

        .form-text { font-size: .76rem; }

        /* ---------- Tabs / pills ---------- */
        .nav-tabs { border-bottom: 1px solid var(--kan-border); }

        .nav-tabs .nav-link {
            border: none;
            color: var(--kan-muted);
            font-weight: 500;
            font-size: .84rem;
            padding: .5rem .9rem;
            border-radius: 0;
        }

        .nav-tabs .nav-link.active {
            color: var(--kan-navy);
            background: transparent;
            border-bottom: 2px solid var(--kan-navy);
        }

        .nav-pills .nav-link {
            color: var(--kan-muted);
            font-weight: 500;
            font-size: .84rem;
            border-radius: 999px;
            padding: .4rem 1rem;
        }

        .nav-pills .nav-link.active {
            background: var(--kan-navy);
            color: #fff;
        }

        /* ---------- Tables ---------- */
        .table { margin-bottom: 0; font-size: .875rem; }
        .table thead th {
            text-transform: uppercase;
            font-size: .68rem;
            letter-spacing: .04em;
            font-weight: 600;
            color: var(--kan-muted);
            border-bottom: 1px solid var(--kan-border);
            padding: .8rem 1rem;
        }
        .table td { padding: .7rem 1rem; vertical-align: middle; border-color: var(--kan-border); }
        .table-hover tbody tr:hover { background-color: var(--kan-hover-bg); }

        /* ---------- Badges ---------- */
        .badge { font-weight: 500; font-size: .72rem; padding: .35em .6em; border-radius: 6px; }

        .badge-accent { background-color: var(--kan-navy-soft); color: var(--kan-navy); }

        /* Перекриваємо системний Bootstrap-акцент (синій/бірюзовий за
           замовчуванням), щоб будь-який невитрачений компонент (пагінація,
           фокус-ринги, чекбокси) теж використовував колір теми. */
        [data-bs-theme=light], :root {
            --bs-primary: var(--kan-navy);
            --bs-primary-rgb: 37, 99, 235;
            --bs-info: var(--kan-navy);
            --bs-info-rgb: 37, 99, 235;
            --bs-link-color: var(--kan-navy);
            --bs-link-color-rgb: 37, 99, 235;
            --bs-link-hover-color: var(--kan-navy-hover);
            --bs-focus-ring-color: rgba(37, 99, 235, .25);
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
            border: 1px solid var(--kan-border);
            border-radius: var(--kan-radius);
            background: var(--kan-surface);
            padding: 1.1rem 1.25rem;
            box-shadow: var(--kan-shadow);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            background: var(--kan-navy-soft);
            color: var(--kan-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .stat-value { font-size: 1.4rem; font-weight: 700; line-height: 1.1; color: var(--kan-ink); }
        .stat-label { font-size: .78rem; color: var(--kan-muted); margin-top: 2px; }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: .9rem 1rem;
            border: 1px solid var(--kan-border);
            border-radius: var(--kan-radius);
            background: var(--kan-surface);
            color: var(--kan-ink);
            text-decoration: none;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }

        .quick-link:hover {
            border-color: var(--kan-navy);
            box-shadow: var(--kan-shadow);
            color: var(--kan-ink);
        }

        .quick-link .stat-icon { background: var(--kan-hover-bg); color: var(--kan-muted); }
        .quick-link:hover .stat-icon { background: var(--kan-navy-soft); color: var(--kan-navy); }
    </style>
</head>
<body>
@auth
    <div class="d-flex">
        <nav class="admin-sidebar p-3 d-flex flex-column" style="width: 250px;">
            <div class="admin-brand px-2 py-2 mb-2">
                <div class="admin-brand-mark">KAN</div>
                <span>Admin</span>
            </div>

            <div class="admin-nav-section">Загальне</div>
            <div class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Дашборд</a>
                <a class="nav-link {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.about.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><i class="bi bi-gear"></i> Налаштування сайту</a>
            </div>

            <div class="admin-nav-section">Контент</div>
            <div class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}"><i class="bi bi-newspaper"></i> Новини</a>
                <a class="nav-link {{ request()->routeIs('admin.news-tags.*') ? 'active' : '' }}" href="{{ route('admin.news-tags.index') }}"><i class="bi bi-tags"></i> Теги новин</a>
                <a class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" href="{{ route('admin.projects.index') }}"><i class="bi bi-buildings"></i> Проєкти</a>
                <a class="nav-link {{ request()->routeIs('admin.project-categories.*') ? 'active' : '' }}" href="{{ route('admin.project-categories.index') }}"><i class="bi bi-diagram-3"></i> Категорії проєктів</a>
                <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}"><i class="bi bi-briefcase"></i> Послуги</a>
                {{-- сюди додаватимуться пункти меню наступних модулів: Вакансії... --}}
            </div>

            <div class="mt-auto px-2 pt-3 border-top" style="border-color: var(--kan-border) !important;">
                <a href="{{ url('/') }}" target="_blank" class="nav-link p-0 pt-3" style="color:var(--kan-muted);font-size:.78rem;">
                    <i class="bi bi-box-arrow-up-right"></i> Переглянути сайт
                </a>
            </div>
        </nav>
        <div class="flex-grow-1">
            <header class="admin-header d-flex justify-content-between align-items-center px-4 py-3">
                <div class="page-title">@yield('title', 'Адмінка')</div>
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-user-chip">
                        <div class="admin-user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                        <span class="text-muted small pe-1">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-right"></i> Вийти</button>
                    </form>
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
</body>
</html>
