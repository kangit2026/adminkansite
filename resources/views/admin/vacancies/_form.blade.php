{{-- Поля вакансії; кнопки збереження — у бічній панелі (create / edit), прив'язані до форми через form="vacancy-form". --}}
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
        @include('admin.vacancies._list-field', ['name' => 'tasks', 'id' => 'v-tasks', 'label' => 'Чим ви займатиметеся', 'hint' => 'Обов’язково, 5–7 пунктів. Один пункт — одна дія.'])
        @include('admin.vacancies._list-field', ['name' => 'requirements', 'id' => 'v-requirements', 'label' => 'Що для нас важливо', 'hint' => 'Досвід, знання, навички.'])
        @include('admin.vacancies._list-field', ['name' => 'nice_to_have', 'id' => 'v-nice', 'label' => 'Буде перевагою', 'hint' => 'Необов’язково.'])
        @include('admin.vacancies._list-field', ['name' => 'offer', 'id' => 'v-offer', 'label' => 'Що ми пропонуємо', 'hint' => 'Необов’язково.'])
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('listField', (initial) => ({
            seq: 0,
            items: [],
            init() {
                this.items = initial.map((t) => this.make(t));
            },
            make(text = '') { return { key: ++this.seq, text }; },
            resize(el) { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px'; },
            focus(i) {
                this.$nextTick(() => {
                    const el = this.$root.querySelectorAll('textarea')[i];
                    if (el) { el.focus(); this.resize(el); }
                });
            },
            add(after) {
                this.items.splice(after + 1, 0, this.make());
                this.focus(after + 1);
            },
            remove(i, focusPrev = false) {
                if (this.items.length === 1) { this.items[0].text = ''; return; }
                this.items.splice(i, 1);
                if (focusPrev) this.focus(Math.max(0, i - 1));
            },
            move(i, d) {
                const j = i + d;
                if (j < 0 || j >= this.items.length) return;
                [this.items[i], this.items[j]] = [this.items[j], this.items[i]];
            },
            // вставка кількох рядків (з Word, Notion, Google Docs) розбивається на окремі пункти
            paste(e, i) {
                const text = (e.clipboardData || window.clipboardData).getData('text');
                if (!/\r?\n/.test(text.trim())) return;
                e.preventDefault();
                const parts = text.split(/\r?\n/)
                    .map((l) => l.replace(/^\s*[•\-—–*·▪●]\s*/, '').replace(/^\s*\d+[.)]\s+/, '').trim())
                    .filter(Boolean);
                if (!parts.length) return;
                const cur = this.items[i];
                if (cur.text.trim() === '') { cur.text = parts.shift(); }
                this.items.splice(i + 1, 0, ...parts.map((t) => this.make(t)));
                this.$nextTick(() => this.$root.querySelectorAll('textarea').forEach((el) => this.resize(el)));
            },
        }));
    });
</script>
