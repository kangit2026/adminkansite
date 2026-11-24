@extends('admin.layout')

@section('title', 'Квіз заявки')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="h3 mb-0">Квіз заявки на головній</h1>
    <a href="{{ route('admin.contact-submissions.index', ['type' => 'apartment']) }}" class="btn btn-outline-secondary btn-sm">Заявки з квізу</a>
</div>
<p class="text-muted">
    Питання блоку «Розкажіть, що вас цікавить». Перша відповідь вирішує, які питання підуть далі (гілка).
    Вимкнений варіант зникає з сайту, але лишається в старих заявках. Усі відповіді потрапляють у заявку окремими полями.
</p>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
@endif

<div x-data="quizEditor(@js($quiz))">
    <form method="POST" action="{{ route('admin.quiz.update') }}" @submit="$refs.quiz.value = JSON.stringify(q)">
        @csrf
        @method('PUT')
        <input type="hidden" name="quiz" x-ref="quiz">

        <div class="card mb-4">
            <div class="card-header fw-semibold">Перше питання</div>
            <div class="card-body">
                <div x-data="{ step: q.intent }">@include('admin.quiz._step', ['intent' => true])</div>
                <div class="form-text">Кожен варіант першого питання відкриває свою гілку нижче. Новий варіант — нова (порожня) гілка.</div>
            </div>
        </div>

        <template x-for="opt in q.intent.options" :key="opt.value">
            <div class="card mb-4" x-data="{ key: opt.value }">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Гілка: <span x-text="opt.label.uk || opt.value"></span></span>
                    <span class="badge" :class="opt.enabled ? 'text-bg-success' : 'text-bg-secondary'" x-text="opt.enabled ? 'показується' : 'вимкнена'"></span>
                </div>
                <div class="card-body">
                    <template x-for="(step, si) in q.paths[key]" :key="step.id">
                        <div>@include('admin.quiz._step', ['intent' => false])</div>
                    </template>
                    <p class="text-muted small" x-show="!q.paths[key].length">У цій гілці питань немає: після першої відповіді людина одразу залишає контакти.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="addStep(key)"><i class="bi bi-plus-lg"></i> Додати питання в гілку</button>
                </div>
            </div>
        </template>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Зберегти</button>
        </div>
    </form>

    @if ($isCustom)
        <form method="POST" action="{{ route('admin.quiz.reset') }}" class="mt-3" onsubmit="return confirm('Повернути типовий квіз? Ваші зміни буде втрачено.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link text-danger p-0">Повернути типовий квіз</button>
        </form>
    @endif
</div>
@endsection

@push('admin-scripts')
<script>
    document.addEventListener('alpine:init', () => {
        const uid = (p) => p + '-' + Math.random().toString(36).slice(2, 7);
        const empty = () => ({ uk: '', en: '' });

        Alpine.data('quizEditor', (initial) => ({
            q: initial,
            move(list, i, d) {
                const j = i + d;
                if (j < 0 || j >= list.length) return;
                [list[i], list[j]] = [list[j], list[i]];
            },
            addOption(step, isIntent) {
                const o = { value: uid('o'), label: empty(), enabled: true };
                step.options.push(o);
                if (isIntent) this.q.paths[o.value] = [];
            },
            removeOption(step, i, isIntent) {
                const o = step.options[i];
                if (isIntent) {
                    const n = (this.q.paths[o.value] || []).length;
                    if (!confirm('Видалити варіант разом із гілкою' + (n ? ' (питань: ' + n + ')' : '') + '? Щоб лише сховати його на сайті — вимкніть перемикач.')) return;
                    delete this.q.paths[o.value];
                } else if (!confirm('Видалити варіант? Щоб лише сховати його на сайті — вимкніть перемикач.')) return;
                step.options.splice(i, 1);
            },
            addStep(key) {
                this.q.paths[key].push({
                    id: uid('q'), label: empty(), question: empty(), hint: empty(),
                    options: [{ value: uid('o'), label: empty(), enabled: true }],
                });
            },
            removeStep(key, i) {
                if (!confirm('Видалити питання разом із варіантами?')) return;
                const [gone] = this.q.paths[key].splice(i, 1);
                if (this.q.project_step === gone.id) this.q.project_step = null;
            },
        }));
    });
</script>
@endpush
