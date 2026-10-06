<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.projects.*') && ! request()->routeIs('admin.projects.seo.*') ? 'active' : '' }}" href="{{ route('admin.projects.index') }}">Список проєктів</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.projects.seo.*') ? 'active' : '' }}" href="{{ route('admin.projects.seo.edit') }}">SEO (/projects)</a>
    </li>
</ul>
