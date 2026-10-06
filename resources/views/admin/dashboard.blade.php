@extends('admin.layout')

@section('title', 'Дашборд')

@section('content')
<h1 class="h4 mb-1">Вітаємо, {{ auth()->user()->name }}!</h1>
<p class="text-muted mb-4">Короткий огляд контенту сайту.</p>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-newspaper"></i></div>
            <div>
                <div class="stat-value">{{ $stats['news'] }}</div>
                <div class="stat-label">Новин ({{ $stats['newsPublished'] }} опубліковано)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-buildings"></i></div>
            <div>
                <div class="stat-value">{{ $stats['projects'] }}</div>
                <div class="stat-label">Проєктів ({{ $stats['projectsPublished'] }} опубліковано)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-diagram-3"></i></div>
            <div>
                <div class="stat-value">{{ $stats['projectCategories'] }}</div>
                <div class="stat-label">Категорій проєктів · {{ $stats['newsTags'] }} тегів новин</div>
            </div>
        </div>
    </div>
</div>

<h2 class="h6 text-uppercase mb-3" style="font-size:.7rem;letter-spacing:.08em;font-weight:700;color:var(--kan-muted-soft);">Швидкі дії</h2>
<div class="row g-3">
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.news.create') }}" class="quick-link">
            <div class="stat-icon"><i class="bi bi-plus-lg"></i></div>
            <div class="small fw-medium">Додати новину</div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.projects.create') }}" class="quick-link">
            <div class="stat-icon"><i class="bi bi-plus-lg"></i></div>
            <div class="small fw-medium">Додати проєкт</div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.about.edit') }}" class="quick-link">
            <div class="stat-icon"><i class="bi bi-info-circle"></i></div>
            <div class="small fw-medium">Про компанію</div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.settings.edit') }}" class="quick-link">
            <div class="stat-icon"><i class="bi bi-house"></i></div>
            <div class="small fw-medium">Головна</div>
        </a>
    </div>
</div>
@endsection
