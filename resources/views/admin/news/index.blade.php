@extends('admin.layout')

@section('title', 'Новини')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Новини</h1>
    <a href="{{ route('admin.news.create') }}" class="btn btn-dark">+ Додати новину</a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr>
                <th style="width:60px;"></th>
                <th>Заголовок</th>
                <th>Теги</th>
                <th>Статус</th>
                <th>Дата публікації</th>
                <th style="width:160px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($news as $item)
                <tr>
                    <td>
                        @if ($item->picture)
                            <img src="{{ asset('storage/'.$item->picture) }}" alt="" style="width:48px;height:48px;object-fit:cover;" class="rounded border">
                        @endif
                    </td>
                    <td>{{ $item->getTranslation('title', 'uk') }}</td>
                    <td>
                        @foreach ($item->tags as $tag)
                            <span class="badge bg-light text-dark border">{{ $tag->getTranslation('name', 'uk') }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if ($item->status === 'published')
                            <span class="badge bg-success">Опубліковано</span>
                        @else
                            <span class="badge bg-secondary">Чернетка</span>
                        @endif
                        @if ($item->big_picture)
                            <span class="badge badge-accent">big</span>
                        @endif
                    </td>
                    <td>{{ optional($item->published_at)->format('d.m.Y H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="Редагувати">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.news.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Видалити новину?')">
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
                    <td colspan="6" class="text-center text-muted py-4">Новин ще немає</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $news->links('pagination::bootstrap-5') }}
</div>
@endsection
