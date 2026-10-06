<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.news.*') && ! request()->routeIs('admin.news.seo.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">Список новин</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.news.seo.*') ? 'active' : '' }}" href="{{ route('admin.news.seo.edit') }}">SEO (/news)</a>
    </li>
</ul>
