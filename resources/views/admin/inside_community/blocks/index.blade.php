@extends('admin.layout')

@section('title', 'Inside Community — блоки')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Inside Community — принципи та блоки</h1>
        <p class="text-muted small mb-0">10 принципів, статистика, стрічки та фінальний блок. Можна додавати нові блоки.</p>
    </div>
    <a href="{{ route('admin.inside-community.blocks.create') }}" class="btn btn-dark">+ Додати блок</a>
</div>

@include('admin._inside_community_tabs')

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr>
                <th style="width:60px;"></th>
                <th>Тип</th>
                <th>#</th>
                <th>Заголовок / значення</th>
                <th>Layout</th>
                <th>Порядок</th>
                <th>Статус</th>
                <th style="width:160px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($blocks as $item)
                <tr>
                    <td>
                        @if ($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}" alt="" style="width:48px;height:48px;object-fit:cover;" class="rounded border">
                        @endif
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $item->type }}</span></td>
                    <td>{{ $item->number ?? '—' }}</td>
                    <td>
                        @if ($item->type === 'stat')
                            {{ $item->getTranslation('stat_value', 'uk') ?: $item->getTranslation('stat_label', 'uk') }}
                        @elseif ($item->type === 'highlight')
                            {{ $item->getTranslation('highlight_big', 'uk') }}
                        @else
                            {{ Str::limit($item->getTranslation('title', 'uk'), 60) }}
                        @endif
                    </td>
                    <td class="text-muted small">{{ $item->layout ?? '—' }}</td>
                    <td>{{ $item->sort_order }}</td>
                    <td>
                        @if ($item->status === 'published')
                            <span class="badge bg-success">Опубліковано</span>
                        @else
                            <span class="badge bg-secondary">Чернетка</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.inside-community.blocks.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="Редагувати">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.inside-community.blocks.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Видалити блок?')">
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
                    <td colspan="8" class="text-center text-muted py-4">Блоків ще немає. Запустіть seeder або додайте вручну.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
