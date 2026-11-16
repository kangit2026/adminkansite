@extends('admin.layout')

@section('title', 'Кандидат')

@section('content')
<a href="{{ route('admin.candidates.index') }}" class="small text-decoration-none d-inline-flex align-items-center gap-1 mb-2">
    <i class="bi bi-chevron-left"></i> Усі кандидати
</a>
<div class="d-flex align-items-center gap-3 flex-wrap mb-1">
    <h1 class="h3 mb-0">{{ $candidate->name }}</h1>
    <span class="hr-pill hr-pill-{{ $candidate->status }}">{{ $candidate->statusLabel() }}</span>
</div>
<p class="text-muted mb-4">
    @if ($candidate->vacancy_id || $candidate->vacancy_title)
        Відгук на вакансію «{{ $candidate->vacancyLabel() }}»
    @else
        Анкета до загальної бази{{ $candidate->direction ? ' · напрям: '.$candidate->direction : '' }}
    @endif
    · {{ $candidate->created_at->format('d.m.Y') }} о {{ $candidate->created_at->format('H:i') }} · з сайту
</p>

@include('admin._hr_alerts')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header fw-semibold">Контакти</div>
            <div class="card-body py-2">
                <dl class="row mb-0">
                    <dt class="col-sm-3 fw-normal text-muted py-2">Телефон</dt>
                    <dd class="col-sm-9 py-2 mb-0"><a href="tel:{{ preg_replace('/[^\d+]/', '', $candidate->phone) }}" class="text-decoration-none fw-medium">{{ $candidate->phone }}</a></dd>
                    <dt class="col-sm-3 fw-normal text-muted py-2 border-top">Email</dt>
                    <dd class="col-sm-9 py-2 mb-0 border-top"><a href="mailto:{{ $candidate->email }}" class="text-decoration-none fw-medium">{{ $candidate->email }}</a></dd>
                    <dt class="col-sm-3 fw-normal text-muted py-2 border-top">LinkedIn</dt>
                    <dd class="col-sm-9 py-2 mb-0 border-top">
                        @if ($candidate->linkedin)
                            <a href="{{ $candidate->linkedin }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium text-break">{{ preg_replace('#^https?://(www\.)?#', '', $candidate->linkedin) }}</a>
                        @else
                            <span class="text-muted">не вказано</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-semibold">Резюме</div>
            <div class="card-body">
                @if ($candidate->cv_path)
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div>
                                <div class="fw-semibold text-break">{{ $candidate->cvDownloadName() }}</div>
                                <div class="small text-muted">{{ $candidate->cvSizeLabel() }} · відкривається лише після входу в адмінку</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.candidates.cv', $candidate) }}" class="btn btn-dark"><i class="bi bi-download"></i> Завантажити резюме</a>
                    </div>
                @else
                    <span class="text-muted">Резюме не додано.</span>
                @endif
            </div>
        </div>

        @if ($candidate->message)
            <div class="card mb-4">
                <div class="card-header fw-semibold">Коментар кандидата</div>
                <div class="card-body" style="white-space: pre-line;">{{ $candidate->message }}</div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header fw-semibold">Нотатки</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.candidates.notes.store', $candidate) }}" class="mb-3">
                    @csrf
                    <label class="form-label" for="note-text">Додати нотатку</label>
                    <textarea id="note-text" name="text" rows="3" class="form-control mb-2" required maxlength="5000"
                        placeholder="Наприклад: телефонувала, домовились про співбесіду в четвер о 15:00">{{ old('text') }}</textarea>
                    <button type="submit" class="btn btn-dark">Додати нотатку</button>
                </form>

                <ol class="list-unstyled mb-0 border-top">
                    @foreach ($candidate->notes as $note)
                        <li class="py-2 border-bottom">
                            <div class="small text-muted">
                                <span class="fw-semibold text-body">{{ $note->is_system && ! $note->user ? 'Система' : ($note->user?->name ?? 'Користувача видалено') }}</span>
                                · {{ $note->created_at->format('d.m.Y H:i') }}
                            </div>
                            <div class="{{ $note->is_system ? 'hr-note-system' : '' }}" style="white-space: pre-line;">{{ $note->text }}</div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header fw-semibold">Робота з кандидатом</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" class="mb-3">
                    @csrf
                    @method('PUT')
                    <label class="form-label" for="c-status">Статус</label>
                    <select id="c-status" name="status" class="form-select" onchange="this.form.submit()">
                        @foreach (\App\Models\Candidate::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected($candidate->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn btn-sm btn-outline-secondary mt-2">Зберегти</button></noscript>
                </form>
                <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" class="mb-3">
                    @csrf
                    @method('PUT')
                    <label class="form-label" for="c-responsible">Відповідальний</label>
                    <select id="c-responsible" name="responsible_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Не призначено</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected($candidate->responsible_id === $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn btn-sm btn-outline-secondary mt-2">Зберегти</button></noscript>
                </form>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Остання дія</span>
                    <span>{{ $candidate->last_activity_at?->format('d.m.Y H:i') ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-semibold">Вакансія</div>
            <div class="card-body small">
                @if ($candidate->vacancy)
                    <a href="{{ route('admin.vacancies.edit', $candidate->vacancy) }}" class="fw-semibold text-decoration-none d-block mb-2" style="font-size:.9375rem;">{{ $candidate->vacancy->title }}</a>
                    <dl class="row mb-0">
                        <dt class="col-5 fw-normal text-muted">Напрям</dt>
                        <dd class="col-7 text-end mb-1">{{ $candidate->vacancy->direction?->name }}</dd>
                        @if ($candidate->vacancy->project)
                            <dt class="col-5 fw-normal text-muted">Проєкт</dt>
                            <dd class="col-7 text-end mb-1">{{ $candidate->vacancy->project }}</dd>
                        @endif
                        <dt class="col-5 fw-normal text-muted">Зайнятість</dt>
                        <dd class="col-7 text-end mb-1">{{ $candidate->vacancy->employmentLabel() }}</dd>
                        <dt class="col-5 fw-normal text-muted">Вакансія</dt>
                        <dd class="col-7 text-end mb-1"><span class="hr-pill hr-pill-{{ $candidate->vacancy->status }}">{{ $candidate->vacancy->statusLabel() }}</span></dd>
                        <dt class="col-5 fw-normal text-muted">Джерело</dt>
                        <dd class="col-7 text-end mb-0">Сайт</dd>
                    </dl>
                @elseif ($candidate->vacancy_title)
                    <div class="fw-semibold mb-1">{{ $candidate->vacancy_title }}</div>
                    <div class="text-muted">Вакансію видалено з адмінки.</div>
                @else
                    <div class="fw-semibold mb-1">{{ \App\Models\Candidate::GENERAL_LABEL }}</div>
                    <div class="text-muted">Кандидат не відгукувався на конкретну вакансію{{ $candidate->direction ? ', напрям: '.$candidate->direction : '' }}.</div>
                @endif
            </div>
        </div>

        <div class="card mb-4" style="border-color:#f3c4c4;">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}"
                    onsubmit="return confirm('Видалити кандидата разом із резюме? Це не можна скасувати.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">Видалити кандидата</button>
                </form>
                <p class="small text-muted mb-0 mt-2">Разом із резюме, без відновлення. Наприклад, якщо кандидат попросив видалити свої дані.</p>
            </div>
        </div>
    </div>
</div>
@endsection
