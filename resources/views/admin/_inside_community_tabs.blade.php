<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.inside-community.edit') ? 'active' : '' }}" href="{{ route('admin.inside-community.edit') }}">Сторінка</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.inside-community.blocks.*') ? 'active' : '' }}" href="{{ route('admin.inside-community.blocks.index') }}">Принципи та блоки</a>
    </li>
</ul>
