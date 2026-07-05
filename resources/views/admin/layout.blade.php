<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Адмінка') — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        body { background-color: #f5f6f8; }
        .admin-sidebar { min-height: 100vh; background: #1c1f26; }
        .admin-sidebar a { color: #c9ccd3; }
        .admin-sidebar a.active, .admin-sidebar a:hover { color: #fff; background: #2a2e37; }
    </style>
</head>
<body>
@auth
    <div class="d-flex">
        <nav class="admin-sidebar p-3" style="width: 240px;">
            <div class="text-white fs-5 fw-semibold mb-4">KAN Admin</div>
            <div class="nav flex-column">
                <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Дашборд</a>
                <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">Налаштування сайту</a>
                <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">Новини</a>
                <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('admin.news-tags.*') ? 'active' : '' }}" href="{{ route('admin.news-tags.index') }}">Теги новин</a>
                {{-- сюди додаватимуться пункти меню наступних модулів: Проекти, Вакансії... --}}
            </div>
        </nav>
        <div class="flex-grow-1">
            <header class="d-flex justify-content-between align-items-center bg-white border-bottom px-4 py-2">
                <div></div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Вийти</button>
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
