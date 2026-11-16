@extends('admin.layout')

@section('title', 'Користувачі')

@section('content')
<h1 class="h3 mb-4">Користувачі</h1>

@include('admin._hr_alerts')

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <span class="fw-semibold">Користувачі</span>
        <span class="badge badge-accent">Лише для адміністратора</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ім’я</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Доступ</th>
                    <th style="width:300px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr x-data="{ pass: false, rename: false }">
                        <td class="fw-semibold">{{ $user->name }}@if ($user->is(auth()->user())) <span class="small text-muted fw-normal">(це ви)</span>@endif</td>
                        <td class="small text-muted text-break">{{ $user->email }}</td>
                        <td class="small">{{ $user->hasRole('admin') ? 'Адміністратор' : ($user->hasRole('hr') ? 'HR' : '—') }}</td>
                        <td class="small {{ $user->is_active ? 'text-success' : 'text-muted' }}">{{ $user->is_active ? 'Активний' : 'Вимкнено' }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-secondary" x-on:click="rename = ! rename; pass = false">Ім’я</button>
                                @unless ($user->is(auth()->user()))
                                    <button type="button" class="btn btn-sm btn-outline-secondary" x-on:click="pass = ! pass; rename = false">Змінити пароль</button>
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">{{ $user->is_active ? 'Вимкнути' : 'Увімкнути' }}</button>
                                    </form>
                                @endunless
                            </div>
                            {{-- x-show — на обгортці: .d-flex з !important перебиває display:none від Alpine --}}
                            <div x-show="rename" x-cloak>
                                <form method="POST" action="{{ route('admin.users.rename', $user) }}" class="d-flex gap-2 mt-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $user->name }}" maxlength="255" required aria-label="Ім’я співробітника">
                                    <button type="submit" class="btn btn-sm btn-dark">Зберегти</button>
                                </form>
                            </div>
                            @unless ($user->is(auth()->user()))
                                <div x-show="pass" x-cloak>
                                    <form method="POST" action="{{ route('admin.users.password', $user) }}" class="d-flex gap-2 mt-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Новий пароль, від 8 символів" minlength="8" required autocomplete="new-password" aria-label="Новий пароль">
                                        <button type="submit" class="btn btn-sm btn-dark">Зберегти</button>
                                    </form>
                                </div>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">
        <div class="fw-semibold small mb-2">Додати користувача</div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-3">
                <label class="form-label" for="u-name">Ім’я</label>
                <input type="text" id="u-name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="u-email">Email</label>
                <input type="email" id="u-email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="255">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="u-pass">Пароль</label>
                <input type="password" id="u-pass" name="password" class="form-control" minlength="8" required autocomplete="new-password">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="u-role">Роль</label>
                <select id="u-role" name="role" class="form-select">
                    <option value="hr" @selected(old('role', 'hr') === 'hr')>HR</option>
                    <option value="admin" @selected(old('role') === 'admin')>Адміністратор</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Додати</button>
            </div>
        </form>
        <p class="small text-muted mb-0 mt-3">HR бачить лише «Вакансії», «Кандидати» і «Налаштування HR» і сам користувачів не додає. Інші розділи сайту йому недоступні.</p>
    </div>
</div>
@endsection
