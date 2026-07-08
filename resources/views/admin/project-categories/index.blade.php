@extends('admin.layout')

@section('title', 'Категорії проєктів')

@section('content')
<h1 class="h3 mb-4">Категорії проєктів</h1>

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
    <div class="card-header fw-semibold">Додати категорію</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.project-categories.store') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Назва (UA)</label>
                <input type="text" name="name[uk]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Назва (EN)</label>
                <input type="text" name="name[en]" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Порядок</label>
                <input type="number" name="sort_order" class="form-control" value="0">
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
                <th style="width:100px;">Порядок</th>
                <th style="width:160px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td><input type="text" name="name[uk]" form="update-category-{{ $category->id }}" class="form-control form-control-sm" value="{{ $category->getTranslation('name', 'uk') }}"></td>
                    <td><input type="text" name="name[en]" form="update-category-{{ $category->id }}" class="form-control form-control-sm" value="{{ $category->getTranslation('name', 'en') }}"></td>
                    <td><input type="number" name="sort_order" form="update-category-{{ $category->id }}" class="form-control form-control-sm" value="{{ $category->sort_order }}"></td>
                    <td class="text-end">
                        <button type="submit" form="update-category-{{ $category->id }}" class="btn btn-sm btn-outline-secondary" title="Зберегти">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button type="submit" form="delete-category-{{ $category->id }}" class="btn btn-sm btn-outline-danger" title="Видалити">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Категорій ще немає</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach ($categories as $category)
    <form id="update-category-{{ $category->id }}" method="POST" action="{{ route('admin.project-categories.update', $category) }}">
        @csrf
        @method('PUT')
    </form>
    <form id="delete-category-{{ $category->id }}" method="POST" action="{{ route('admin.project-categories.destroy', $category) }}"
        onsubmit="return confirm('Видалити категорію?')">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection
