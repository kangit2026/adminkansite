<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.services.*') && ! request()->routeIs('admin.services.seo.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">Список послуг</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.services.seo.*') ? 'active' : '' }}" href="{{ route('admin.services.seo.edit') }}">SEO (/services)</a>
    </li>
</ul>
