@extends('admin.layout')

@section('title', 'Послуги')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Послуги (/services)</h1>
    <a href="{{ route('admin.services.create') }}" class="btn btn-dark">+ Додати послугу</a>
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
                <th>Посилання</th>
                <th>Порядок</th>
                <th>Статус</th>
                <th style="width:160px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $item)
                <tr>
                    <td>
                        @if ($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}" alt="" style="width:48px;height:48px;object-fit:cover;" class="rounded border">
                        @endif
                    </td>
                    <td>{{ $item->getTranslation('title', 'uk') }}</td>
                    <td class="text-muted small">{{ $item->link }}</td>
                    <td>{{ $item->sort_order }}</td>
                    <td>
                        @if ($item->status === 'published')
                            <span class="badge bg-success">Опубліковано</span>
                        @else
                            <span class="badge bg-secondary">Чернетка</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.services.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="Редагувати">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.services.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Видалити послугу?')">
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
                    <td colspan="6" class="text-center text-muted py-4">Послуг ще немає</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
