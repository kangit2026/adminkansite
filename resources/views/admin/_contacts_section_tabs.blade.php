<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.contacts.edit', 'admin.contacts.update') ? 'active' : '' }}" href="{{ route('admin.contacts.edit') }}">Сторінка /contacts</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.contacts.site.*') ? 'active' : '' }}" href="{{ route('admin.contacts.site.edit') }}">Контакти та футер</a>
    </li>
</ul>
