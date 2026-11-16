@extends('admin.layout')

@section('title', 'Налаштування HR')

@section('content')
<h1 class="h3 mb-4">Налаштування HR</h1>

@include('admin._hr_alerts')

<div class="card mb-4">
    <div class="card-header fw-semibold">Сповіщення і посилання</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.hr-settings.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label" for="s-emails">Email для сповіщень</label>
                <input type="text" id="s-emails" name="notify_emails" class="form-control" maxlength="500"
                    value="{{ old('notify_emails', $page->notify_emails) }}" placeholder="name@example.com, name2@example.com">
                <div class="form-text">Кілька адрес — через кому. Сюди приходить лист про кожен новий відгук.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="s-referral">Посилання на реферальну програму</label>
                <input type="url" id="s-referral" name="referral_url" class="form-control" maxlength="500"
                    value="{{ old('referral_url', $page->referral_url) }}" placeholder="https://…">
                <div class="form-text">Якщо порожньо — блок «Знаєте сильного спеціаліста?» на сайті не показується.</div>
            </div>
            <button type="submit" class="btn btn-dark">Зберегти</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Напрями</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Назва</th>
                    <th style="width:110px;">Порядок</th>
                    <th>Вакансії</th>
                    <th style="width:110px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($directions as $direction)
                    <tr>
                        <td><input type="text" name="name" form="update-direction-{{ $direction->id }}" class="form-control form-control-sm" value="{{ $direction->name }}" required maxlength="100" aria-label="Назва напряму"></td>
                        <td><input type="number" name="sort_order" form="update-direction-{{ $direction->id }}" class="form-control form-control-sm" value="{{ $direction->sort_order }}" aria-label="Порядок"></td>
                        <td class="small text-muted">
                            @if ($direction->vacancies_count)
                                {{ \App\Support\Plural::uk($direction->vacancies_count, 'вакансія', 'вакансії', 'вакансій') }},
                                {{ \App\Support\Plural::uk($direction->active_vacancies_count, 'активна', 'активні', 'активних') }}
                            @else
                                вакансій немає
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="submit" form="update-direction-{{ $direction->id }}" class="btn btn-sm btn-outline-secondary" title="Зберегти">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="submit" form="delete-direction-{{ $direction->id }}" class="btn btn-sm btn-outline-danger"
                                title="{{ $direction->vacancies_count ? 'Не можна видалити: є вакансії' : 'Видалити' }}" @disabled($direction->vacancies_count)>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Напрямів ще немає</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">
        <form method="POST" action="{{ route('admin.career-directions.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-6">
                <label class="form-label" for="d-name">Новий напрям</label>
                <input type="text" id="d-name" name="name" class="form-control" placeholder="Наприклад: Продажі" required maxlength="100">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="d-order">Порядок</label>
                <input type="number" id="d-order" name="sort_order" class="form-control" value="{{ ($directions->max('sort_order') ?? 0) + 1 }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-secondary w-100">Додати напрям</button>
            </div>
        </form>
        <p class="small text-muted mb-0 mt-3">Напрям не можна видалити, поки в ньому є вакансії. У фільтрі на сайті видно лише напрями з активними вакансіями.</p>
    </div>
</div>

@foreach ($directions as $direction)
    <form id="update-direction-{{ $direction->id }}" method="POST" action="{{ route('admin.career-directions.update', $direction) }}">
        @csrf
        @method('PUT')
    </form>
    <form id="delete-direction-{{ $direction->id }}" method="POST" action="{{ route('admin.career-directions.destroy', $direction) }}"
        onsubmit="return confirm('Видалити напрям?')">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@endsection
