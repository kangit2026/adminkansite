@extends('admin.layout')

@section('title', 'Вакансії')

@section('content')
<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h1 class="h3 mb-1">Вакансії</h1>
        <p class="text-muted mb-0" style="max-width:640px;">
            Активні вакансії видно на сайті в розділі «Кар’єра». Закрита вакансія зникає з сайту,
            а її текст і відгуки залишаються тут — її можна відкрити знову.
        </p>
    </div>
    <a href="{{ route('admin.vacancies.create') }}" class="btn btn-dark">+ Нова вакансія</a>
</div>

@include('admin._hr_alerts')

<ul class="nav nav-pills mb-3">
    @foreach (['' => 'Усі', 'active' => 'Активні', 'draft' => 'Чернетки', 'closed' => 'Закриті'] as $key => $label)
        <li class="nav-item">
            <a class="nav-link {{ (string) $status === $key ? 'active' : '' }}"
                href="{{ route('admin.vacancies.index', $key ? ['status' => $key] : []) }}">
                {{ $label }} <span class="text-muted fw-normal ms-1">{{ $key ? ($counts[$key] ?? 0) : $counts->sum() }}</span>
            </a>
        </li>
    @endforeach
</ul>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Назва</th>
                    <th>Напрям</th>
                    <th>Статус</th>
                    <th>Відгуки</th>
                    <th>Опубліковано</th>
                    <th style="width:220px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vacancies as $vacancy)
                    <tr>
                        <td>
                            <a href="{{ route('admin.vacancies.edit', $vacancy) }}" class="fw-semibold text-decoration-none">{{ $vacancy->title }}</a>
                            <div class="small text-muted">{{ collect([$vacancy->project, $vacancy->employmentLabel()])->filter()->implode(' · ') }}</div>
                        </td>
                        <td>{{ $vacancy->direction->name }}</td>
                        <td><span class="hr-pill hr-pill-{{ $vacancy->status }}">{{ $vacancy->statusLabel() }}</span></td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.candidates.index', ['vacancy' => $vacancy->id]) }}" class="fw-semibold text-decoration-none">{{ $vacancy->candidates_count }}</a>
                            @if ($vacancy->new_candidates_count)
                                <span class="badge badge-accent ms-1">+{{ \App\Support\Plural::uk($vacancy->new_candidates_count, 'новий', 'нові', 'нових') }}</span>
                            @endif
                        </td>
                        <td class="small text-muted text-nowrap">
                            @if ($vacancy->status === 'closed')
                                закрито {{ $vacancy->closed_at?->format('d.m.Y') }}
                            @else
                                {{ $vacancy->published_at?->format('d.m.Y') ?? '—' }}
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            @if ($vacancy->status === 'draft')
                                <form method="POST" action="{{ route('admin.vacancies.publish', $vacancy) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-dark">Опублікувати</button>
                                </form>
                            @elseif ($vacancy->status === 'active')
                                <form method="POST" action="{{ route('admin.vacancies.close', $vacancy) }}" class="d-inline"
                                    onsubmit="return confirm('Закрити вакансію? Вона зникне з сайту, відгуки залишаться.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Закрити</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.vacancies.reopen', $vacancy) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Відкрити знову</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.vacancies.edit', $vacancy) }}" class="btn btn-sm btn-outline-secondary" title="Редагувати">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ $status ? 'Вакансій із таким статусом немає.' : 'Вакансій ще немає.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $vacancies->links('pagination::bootstrap-5') }}
</div>
@endsection
