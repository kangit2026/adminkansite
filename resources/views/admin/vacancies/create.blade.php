@extends('admin.layout')

@section('title', 'Нова вакансія')

@section('content')
<a href="{{ route('admin.vacancies.index') }}" class="small text-decoration-none d-inline-flex align-items-center gap-1 mb-2">
    <i class="bi bi-chevron-left"></i> Усі вакансії
</a>
<h1 class="h3 mb-4">Нова вакансія</h1>

@include('admin._hr_alerts')

<div class="row g-4">
    <div class="col-lg-8">
        <form id="vacancy-form" method="POST" action="{{ route('admin.vacancies.store') }}">
            @csrf
            @include('admin.vacancies._form')
        </form>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header fw-semibold">Публікація</div>
            <div class="card-body d-grid gap-2">
                <button type="submit" form="vacancy-form" name="action" value="publish" class="btn btn-dark">Опублікувати</button>
                <button type="submit" form="vacancy-form" name="action" value="draft" class="btn btn-outline-secondary">Зберегти чернетку</button>
                <p class="small text-muted mb-0 mt-1">Чернетку на сайті не видно — її можна опублікувати пізніше.</p>
            </div>
        </div>
        @include('admin.vacancies._hint')
    </div>
</div>
@endsection
