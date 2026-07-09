@extends('admin.layout')

@section('title', 'Проєкти')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Проєкти</h1>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-dark">+ Додати проєкт</a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr>
                <th style="width:60px;"></th>
                <th>Назва</th>
                <th>Категорія</th>
                <th>Статус</th>
                <th style="width:160px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projects as $item)
                <tr>
                    <td>
                        @if ($item->main_picture)
                            <img src="{{ asset('storage/'.$item->main_picture) }}" alt="" style="width:48px;height:48px;object-fit:cover;" class="rounded border">
                        @endif
                    </td>
                    <td>{{ $item->getTranslation('name', 'uk') }}</td>
                    <td>{{ optional($item->category)->getTranslation('name', 'uk') }}</td>
                    <td>
                        @if ($item->status === 'published')
                            <span class="badge bg-success">Опубліковано</span>
                        @else
                            <span class="badge bg-secondary">Чернетка</span>
                        @endif
                        @if ($item->is_featured)
                            <span class="badge badge-accent">рекомендований</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.projects.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="Редагувати">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Видалити проєкт?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Видалити">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Проєктів ще немає</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $projects->links('pagination::bootstrap-5') }}
</div>
@endsection
