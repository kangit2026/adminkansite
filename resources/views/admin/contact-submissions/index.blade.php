@extends('admin.layout')

@section('title', 'Заявки з форми')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Заявки з форм сайту</h1>
    <a href="{{ route('admin.contacts.edit') }}" class="btn btn-outline-secondary btn-sm">Налаштування /contacts</a>
</div>

<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="{{ route('admin.contact-submissions.index') }}" class="btn btn-sm {{ $type ? 'btn-outline-secondary' : 'btn-secondary' }}">Усі</a>
    @foreach (\App\Models\ContactSubmission::TYPES as $key => $label)
        <a href="{{ route('admin.contact-submissions.index', ['type' => $key]) }}" class="btn btn-sm {{ $type === $key ? 'btn-secondary' : 'btn-outline-secondary' }}">{{ $label }}</a>
    @endforeach
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Форма</th>
                    <th>Ім’я</th>
                    <th>Контакти</th>
                    <th>Повідомлення</th>
                    <th>Сторінка</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $item)
                    <tr>
                        <td class="text-nowrap small text-muted">{{ $item->created_at->format('d.m.Y H:i') }}</td>
                        <td class="small">{{ $item->typeLabel() }}</td>
                        <td>{{ $item->name ?: '—' }}</td>
                        <td class="small">
                            @if ($item->phone)<div>{{ $item->phone }}</div>@endif
                            @if ($item->email)<div><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></div>@endif
                        </td>
                        <td style="max-width:320px;">
                            @foreach ($item->payload ?? [] as $label => $value)
                                <div class="small"><span class="text-muted">{{ $label }}:</span> {{ $value }}</div>
                            @endforeach
                            @if ($item->message)<div class="small text-truncate" title="{{ $item->message }}">{{ Str::limit($item->message, 120) }}</div>@endif
                            @foreach ($item->attachments ?? [] as $i => $file)
                                <div class="small"><a href="{{ route('admin.contact-submissions.file', [$item, $i]) }}">{{ $file['name'] }}</a></div>
                            @endforeach
                        </td>
                        <td class="small text-muted">{{ $item->source_page ?: '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.contact-submissions.update', $item) }}" class="d-flex gap-2 align-items-center">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                                    <option value="new" @selected($item->status === 'new')>Нова</option>
                                    <option value="processed" @selected($item->status === 'processed')>Оброблена</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Заявок ще немає</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($submissions->hasPages())
        <div class="card-footer">{{ $submissions->links() }}</div>
    @endif
</div>
@endsection
