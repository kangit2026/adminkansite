@extends('admin.layout')

@section('title', 'Вакансія')

@section('content')
<a href="{{ route('admin.vacancies.index') }}" class="small text-decoration-none d-inline-flex align-items-center gap-1 mb-2">
    <i class="bi bi-chevron-left"></i> Усі вакансії
</a>
<h1 class="h3 mb-4">{{ $vacancy->title }}</h1>

@include('admin._hr_alerts')

<div class="row g-4">
    <div class="col-lg-8">
        <form id="vacancy-form" method="POST" action="{{ route('admin.vacancies.update', $vacancy) }}">
            @csrf
            @method('PUT')
            @include('admin.vacancies._form')
            <button type="submit" class="btn btn-dark">Зберегти</button>
            <a href="{{ route('admin.vacancies.index') }}" class="btn btn-outline-secondary ms-2">Скасувати</a>
        </form>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header fw-semibold">Публікація</div>
            <div class="card-body">
                <dl class="row small mb-3">
                    <dt class="col-6 fw-normal text-muted">Статус</dt>
                    <dd class="col-6 text-end mb-2"><span class="hr-pill hr-pill-{{ $vacancy->status }}">{{ $vacancy->statusLabel() }}</span></dd>
                    @if ($vacancy->status === 'closed')
                        <dt class="col-6 fw-normal text-muted">Закрито</dt>
                        <dd class="col-6 text-end mb-2">{{ $vacancy->closed_at?->format('d.m.Y') }}</dd>
                    @endif
                    @if ($vacancy->published_at)
                        <dt class="col-6 fw-normal text-muted">Опубліковано</dt>
                        <dd class="col-6 text-end mb-2">{{ $vacancy->published_at->format('d.m.Y') }}</dd>
                    @endif
                    <dt class="col-6 fw-normal text-muted">Відгуки</dt>
                    <dd class="col-6 text-end mb-0">
                        <a href="{{ route('admin.candidates.index', ['vacancy' => $vacancy->id]) }}" class="fw-semibold text-decoration-none">
                            {{ $vacancy->candidates_count }}@if ($vacancy->new_candidates_count), з них {{ \App\Support\Plural::uk($vacancy->new_candidates_count, 'новий', 'нові', 'нових') }}@endif
                        </a>
                    </dd>
                </dl>

                <div class="d-grid gap-2">
                    <button type="submit" form="vacancy-form" class="btn btn-dark">Зберегти</button>

                    @if ($vacancy->status === 'draft')
                        <form method="POST" action="{{ route('admin.vacancies.publish', $vacancy) }}" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">Опублікувати</button>
                        </form>
                    @elseif ($vacancy->status === 'active')
                        <form method="POST" action="{{ route('admin.vacancies.close', $vacancy) }}" class="d-grid"
                            onsubmit="return confirm('Закрити вакансію? Вона зникне з сайту, відгуки залишаться.')">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">Закрити вакансію</button>
                        </form>
                        @if (config('services.site.url'))
                            <a href="{{ config('services.site.url') }}/career/{{ $vacancy->slug }}" target="_blank" rel="noopener" class="btn btn-link btn-sm text-decoration-none">
                                Відкрити на сайті <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        @endif
                    @else
                        <form method="POST" action="{{ route('admin.vacancies.reopen', $vacancy) }}" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">Відкрити знову</button>
                        </form>
                    @endif
                </div>

                <p class="small text-muted mb-0 mt-3">
                    @if ($vacancy->status === 'draft')
                        Чернетку на сайті не видно.
                    @else
                        Закрита вакансія зникає з сайту. Текст і відгуки зберігаються — її можна відкрити знову.
                    @endif
                </p>

                @if ($vacancy->status === 'draft' && $vacancy->candidates_count === 0)
                    <form method="POST" action="{{ route('admin.vacancies.destroy', $vacancy) }}" class="mt-3 pt-3 border-top"
                        onsubmit="return confirm('Видалити чернетку? Це не можна скасувати.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Видалити чернетку</button>
                    </form>
                @endif
            </div>
        </div>
        @include('admin.vacancies._hint')
    </div>
</div>
@endsection
