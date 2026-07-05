@extends('admin.layout')

@section('title', 'Теги новин')

@section('content')
<h1 class="h3 mb-4">Теги новин</h1>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-4">
    <div class="card-header fw-semibold">Додати тег</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.news-tags.store') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Назва (UA)</label>
                <input type="text" name="name[uk]" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Назва (EN)</label>
                <input type="text" name="name[en]" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Додати</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr>
                <th>UA</th>
                <th>EN</th>
                <th style="width:160px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tags as $tag)
                <tr>
                    <td><input type="text" name="name[uk]" form="update-tag-{{ $tag->id }}" class="form-control form-control-sm" value="{{ $tag->getTranslation('name', 'uk') }}"></td>
                    <td><input type="text" name="name[en]" form="update-tag-{{ $tag->id }}" class="form-control form-control-sm" value="{{ $tag->getTranslation('name', 'en') }}"></td>
                    <td class="text-end">
                        <button type="submit" form="update-tag-{{ $tag->id }}" class="btn btn-sm btn-outline-secondary" title="Зберегти">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button type="submit" form="delete-tag-{{ $tag->id }}" class="btn btn-sm btn-outline-danger" title="Видалити">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">Тегів ще немає</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Форми оновлення/видалення винесені за межі таблиці й прив'язані до
     полів через атрибут form="", бо <form> не може огортати <tr>/<td>. --}}
@foreach ($tags as $tag)
    <form id="update-tag-{{ $tag->id }}" method="POST" action="{{ route('admin.news-tags.update', $tag) }}">
        @csrf
        @method('PUT')
    </form>
    <form id="delete-tag-{{ $tag->id }}" method="POST" action="{{ route('admin.news-tags.destroy', $tag) }}"
        onsubmit="return confirm('Видалити тег?')">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection
