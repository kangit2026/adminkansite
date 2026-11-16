@extends('admin.layout')

@section('title', 'Кандидати')

@section('content')
@php
    // фільтри, крім статусу, — щоб вкладки статусів їх зберігали
    $keep = array_filter(['vacancy' => $filters['vacancy'], 'responsible' => $filters['responsible'], 'q' => $filters['q']], fn ($v) => $v !== '');
    $anyFilter = $keep || $filters['status'];
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h1 class="h3 mb-1">Кандидати</h1>
        <p class="text-muted mb-0" style="max-width:640px;">Усі відгуки з сайту: на вакансії й до загальної бази. Нові — вгорі. Про кожен новий відгук HR отримує лист.</p>
    </div>
    <a href="{{ route('admin.candidates.export', request()->query()) }}" class="btn btn-outline-secondary">
        <i class="bi bi-download"></i> Експорт CSV
    </a>
</div>

@include('admin._hr_alerts')

<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $filters['status'] ? '' : 'active' }}" href="{{ route('admin.candidates.index', $keep) }}">
            Усі <span class="text-muted fw-normal ms-1">{{ $counts->sum() }}</span>
        </a>
    </li>
    @foreach (\App\Models\Candidate::STATUSES as $key => $label)
        <li class="nav-item">
            <a class="nav-link {{ $filters['status'] === $key ? 'active' : '' }}" href="{{ route('admin.candidates.index', $keep + ['status' => $key]) }}">
                {{ $label }} <span class="text-muted fw-normal ms-1">{{ $counts[$key] ?? 0 }}</span>
            </a>
        </li>
    @endforeach
</ul>

<form method="GET" action="{{ route('admin.candidates.index') }}" class="row g-3 align-items-end mb-4">
    @if ($filters['status'])
        <input type="hidden" name="status" value="{{ $filters['status'] }}">
    @endif
    <div class="col-md-4">
        <label class="form-label" for="c-q">Пошук</label>
        <input type="search" id="c-q" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Ім’я, телефон або email">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="c-vacancy">Вакансія</label>
        <select id="c-vacancy" name="vacancy" class="form-select" onchange="this.form.submit()">
            <option value="">Усі вакансії</option>
            <option value="general" @selected($filters['vacancy'] === 'general')>{{ \App\Models\Candidate::GENERAL_LABEL }}</option>
            @foreach ($vacancies as $vacancy)
                <option value="{{ $vacancy->id }}" @selected($filters['vacancy'] === (string) $vacancy->id)>
                    {{ $vacancy->title }}{{ $vacancy->status === 'active' ? '' : ' ('.mb_strtolower($vacancy->statusLabel()).')' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="c-responsible">Відповідальний</label>
        <select id="c-responsible" name="responsible" class="form-select" onchange="this.form.submit()">
            <option value="">Усі</option>
            <option value="none" @selected($filters['responsible'] === 'none')>Не призначено</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected($filters['responsible'] === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-dark flex-grow-1">Знайти</button>
        @if ($anyFilter)
            <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline-secondary" title="Скинути фільтри"><i class="bi bi-x-lg"></i></a>
        @endif
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Кандидат</th>
                    <th>Вакансія</th>
                    <th>Телефон</th>
                    <th>Статус</th>
                    <th>Відповідальний</th>
                    <th>Остання дія</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($candidates as $candidate)
                    <tr class="{{ $candidate->status === 'new' ? 'hr-row-new' : '' }}">
                        <td class="small text-nowrap">
                            {{ $candidate->created_at->format('d.m.Y') }}
                            <div class="text-muted">{{ $candidate->created_at->format('H:i') }}</div>
                        </td>
                        <td style="max-width:240px;">
                            <a href="{{ route('admin.candidates.show', $candidate) }}" class="hr-name fw-semibold text-decoration-none">{{ $candidate->name }}</a>
                            <div class="small text-muted text-truncate">{{ $candidate->email }}</div>
                        </td>
                        <td style="max-width:280px;">
                            {{ $candidate->vacancyLabel() }}
                            @if (! $candidate->vacancy_id && $candidate->direction)
                                <div class="small text-muted">напрям: {{ $candidate->direction }}</div>
                            @endif
                        </td>
                        <td class="small text-nowrap">{{ $candidate->phone }}</td>
                        <td><span class="hr-pill hr-pill-{{ $candidate->status }}">{{ $candidate->statusLabel() }}</span></td>
                        <td class="small {{ $candidate->responsible ? '' : 'text-muted' }}">{{ $candidate->responsible?->name ?? 'Не призначено' }}</td>
                        <td class="small text-muted text-nowrap">{{ $candidate->last_activity_at?->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ $anyFilter ? 'Нікого не знайдено. Змініть фільтри або пошук.' : 'Відгуків ще немає.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($candidates->total() > 0)
        <div class="card-footer small text-muted">Показано {{ $candidates->firstItem() }}–{{ $candidates->lastItem() }} з {{ $candidates->total() }}</div>
    @endif
</div>

<div class="mt-3">
    {{ $candidates->links('pagination::bootstrap-5') }}
</div>
@endsection
