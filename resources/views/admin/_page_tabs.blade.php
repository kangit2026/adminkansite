{{-- Перемикач між "Налаштування сайту" і сторінками, що редагуються окремо
     (Про компанію, і далі — Areas/Develop/...). Підключається зверху обох
     форм через @include('admin._page_tabs'). --}}
<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">Головна</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.about.*') ? 'active' : '' }}" href="{{ route('admin.about.edit') }}">Про компанію</a>
    </li>
</ul>
