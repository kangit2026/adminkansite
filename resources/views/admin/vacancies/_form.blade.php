{{-- Поля вакансії; кнопки збереження — у бічній панелі (create / edit), прив'язані до форми через form="vacancy-form". --}}
@php
    $lines = fn (string $field) => old($field, implode("\n", $vacancy->{$field} ?? []));
@endphp

<div class="card mb-4">
    <div class="card-header fw-semibold">Основне</div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label" for="v-title">Назва посади</label>
            <input type="text" id="v-title" name="title" class="form-control" value="{{ old('title', $vacancy->title) }}" required maxlength="255">
        </div>

        <div class="mb-3">
            <label class="form-label" for="v-slug">Адреса сторінки</label>
            @if ($vacancy->slugLocked())
                <div class="form-control bg-light text-muted">/career/{{ $vacancy->slug }}</div>
                <div class="form-text">Вакансію вже публікували, тому адреса не змінюється — інакше зламаються посилання, якими поділилися.</div>
            @else
                <div class="input-group">
                    <span class="input-group-text">/career/</span>
                    <input type="text" id="v-slug" name="slug" class="form-control" value="{{ old('slug', $vacancy->slug) }}" placeholder="створиться з назви" maxlength="255">
                </div>
                <div class="form-text">Латиницею, через дефіс. Після публікації не змінюється.</div>
            @endif
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="v-direction">Напрям</label>
                <select id="v-direction" name="direction_id" class="form-select" required>
                    <option value="">Оберіть напрям</option>
                    @foreach ($directions as $direction)
                        <option value="{{ $direction->id }}" @selected((int) old('direction_id', $vacancy->direction_id) === $direction->id)>{{ $direction->name }}</option>
                    @endforeach
                </select>
                <div class="form-text">Список напрямів — у <a href="{{ route('admin.hr-settings.edit') }}">налаштуваннях HR</a>.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="v-type">Тип зайнятості</label>
                <select id="v-type" name="employment_type" class="form-select" required>
                    @foreach (\App\Models\Vacancy::EMPLOYMENT_TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(old('employment_type', $vacancy->employment_type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="v-project">Проєкт</label>
                <input type="text" id="v-project" name="project" class="form-control" value="{{ old('project', $vacancy->project) }}" maxlength="255">
                <div class="form-text">Необов’язково. Назва проєкту або «житловий проєкт у Києві», «новий девелоперський проєкт». Порожнє поле на сайті не показується.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="v-place">Місце роботи</label>
                <input type="text" id="v-place" name="place" class="form-control" value="{{ old('place', $vacancy->place) }}" placeholder="{{ \App\Models\Vacancy::DEFAULT_PLACE }}" maxlength="255">
                <div class="form-text">Якщо порожньо — {{ \App\Models\Vacancy::DEFAULT_PLACE }}.</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Картка у списку вакансій</div>
    <div class="card-body" x-data="{ n: {{ mb_strlen((string) old('summary', $vacancy->summary)) }} }">
        <div class="d-flex justify-content-between align-items-baseline">
            <label class="form-label" for="v-summary">Коротко для картки</label>
            <span class="small" :class="n > 300 ? 'text-danger fw-semibold' : 'text-muted'" x-text="n + ' / 300'"></span>
        </div>
        <textarea id="v-summary" name="summary" rows="3" class="form-control" required x-on:input="n = $event.target.value.length">{{ old('summary', $vacancy->summary) }}</textarea>
        <div class="form-text">1–2 речення, до 300 символів. Показується у списку вакансій і в пошуку Google.</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Сторінка вакансії</div>
    <div class="card-body">
        <div class="mb-4">
            <label class="form-label" for="v-about">Коротко про роль</label>
            <textarea id="v-about" name="about" rows="7" class="form-control" required>{{ old('about', $vacancy->about) }}</textarea>
            <div class="form-text">2–3 речення про головну задачу. Новий абзац — з порожнього рядка.</div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="v-tasks">Чим ви займатиметеся</label>
            <textarea id="v-tasks" name="tasks" rows="9" class="form-control" required>{{ $lines('tasks') }}</textarea>
            <div class="form-text">Кожен пункт — з нового рядка, 5–7 пунктів.</div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="v-requirements">Що для нас важливо</label>
            <textarea id="v-requirements" name="requirements" rows="6" class="form-control">{{ $lines('requirements') }}</textarea>
            <div class="form-text">Досвід, знання, навички. Кожен пункт — з нового рядка.</div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="v-nice">Буде перевагою</label>
            <textarea id="v-nice" name="nice_to_have" rows="3" class="form-control">{{ $lines('nice_to_have') }}</textarea>
            <div class="form-text">Необов’язково. Кожен пункт — з нового рядка.</div>
        </div>
        <div>
            <label class="form-label" for="v-offer">Що ми пропонуємо</label>
            <textarea id="v-offer" name="offer" rows="5" class="form-control">{{ $lines('offer') }}</textarea>
            <div class="form-text">Необов’язково. Кожен пункт — з нового рядка.</div>
        </div>
    </div>
</div>
