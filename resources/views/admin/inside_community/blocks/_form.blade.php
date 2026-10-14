@csrf

<div class="card mb-4">
    <div class="card-header fw-semibold">Тип та розташування</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Тип блоку</label>
                <select name="type" class="form-select" required>
                    @foreach (['principle' => 'Принцип', 'stat' => 'Статистика / цитата', 'highlight' => 'Акцент (великі цифри)', 'ribbon' => 'Стрічка', 'final' => 'Фінальний блок (10)'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $block->type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Layout (варіант верстки)</label>
                <select name="layout" class="form-select">
                    <option value="">— за замовчуванням —</option>
                    @foreach (['full_flip_first', 'full_flip', 'full_magic2', 'full_magic_offset', 'full_magic_t', 'compact', 'compact_flip', 'default', 'with_icon', 'left', 'special', 'description_only'] as $layout)
                        <option value="{{ $layout }}" @selected(old('layout', $block->layout) === $layout)>{{ $layout }}</option>
                    @endforeach
                </select>
                <div class="form-text">Для нових принципів використовуйте <code>full_flip</code> або <code>compact</code>.</div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Номер (для принципів)</label>
                <input type="number" name="number" min="1" max="99" class="form-control" value="{{ old('number', $block->number) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Порядок</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $block->sort_order) }}">
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Контент</div>
    <div class="card-body" x-data="{ lang: 'uk' }">
        @include('admin._lang_tabs')

        <div x-show="lang === 'uk'">
            <div class="mb-3">
                <label class="form-label">Заголовок (UA)</label>
                <textarea name="title[uk]" rows="2" class="form-control">{{ old('title.uk', $block->getTranslation('title', 'uk')) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Опис (UA)</label>
                <textarea name="description[uk]" rows="4" class="form-control">{{ old('description.uk', $block->getTranslation('description', 'uk')) }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Stat value (UA)</label>
                    <input type="text" name="stat_value[uk]" class="form-control" value="{{ old('stat_value.uk', $block->getTranslation('stat_value', 'uk')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stat label (UA)</label>
                    <textarea name="stat_label[uk]" rows="2" class="form-control">{{ old('stat_label.uk', $block->getTranslation('stat_label', 'uk')) }}</textarea>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">Highlight small (UA)</label>
                    <input type="text" name="highlight_small[uk]" class="form-control" value="{{ old('highlight_small.uk', $block->getTranslation('highlight_small', 'uk')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Highlight big (UA)</label>
                    <textarea name="highlight_big[uk]" rows="2" class="form-control">{{ old('highlight_big.uk', $block->getTranslation('highlight_big', 'uk')) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Highlight medium (UA)</label>
                    <textarea name="highlight_medium[uk]" rows="2" class="form-control">{{ old('highlight_medium.uk', $block->getTranslation('highlight_medium', 'uk')) }}</textarea>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label class="form-label">CTA текст (UA, для final)</label>
                <textarea name="cta_text[uk]" rows="2" class="form-control">{{ old('cta_text.uk', $block->getTranslation('cta_text', 'uk')) }}</textarea>
            </div>
        </div>

        <div x-show="lang === 'en'">
            <div class="mb-3">
                <label class="form-label">Заголовок (EN)</label>
                <textarea name="title[en]" rows="2" class="form-control">{{ old('title.en', $block->getTranslation('title', 'en')) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Опис (EN)</label>
                <textarea name="description[en]" rows="4" class="form-control">{{ old('description.en', $block->getTranslation('description', 'en')) }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Stat value (EN)</label>
                    <input type="text" name="stat_value[en]" class="form-control" value="{{ old('stat_value.en', $block->getTranslation('stat_value', 'en')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stat label (EN)</label>
                    <textarea name="stat_label[en]" rows="2" class="form-control">{{ old('stat_label.en', $block->getTranslation('stat_label', 'en')) }}</textarea>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">Highlight small (EN)</label>
                    <input type="text" name="highlight_small[en]" class="form-control" value="{{ old('highlight_small.en', $block->getTranslation('highlight_small', 'en')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Highlight big (EN)</label>
                    <textarea name="highlight_big[en]" rows="2" class="form-control">{{ old('highlight_big.en', $block->getTranslation('highlight_big', 'en')) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Highlight medium (EN)</label>
                    <textarea name="highlight_medium[en]" rows="2" class="form-control">{{ old('highlight_medium.en', $block->getTranslation('highlight_medium', 'en')) }}</textarea>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label class="form-label">CTA текст (EN, для final)</label>
                <textarea name="cta_text[en]" rows="2" class="form-control">{{ old('cta_text.en', $block->getTranslation('cta_text', 'en')) }}</textarea>
            </div>
        </div>

        <div class="mb-1 mt-3">
            <label class="form-label">Зображення</label>
            @if ($block->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$block->image) }}" alt="" style="max-height:120px;" class="rounded border">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="form-control">
            @include('admin._image_alt', ['name' => 'image_alt', 'attribute' => 'image_alt', 'model' => $block])
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Публікація</div>
    <div class="card-body">
        <label class="form-label">Статус</label>
        <select name="status" class="form-select" style="max-width:240px;">
            <option value="draft" @selected(old('status', $block->status) === 'draft')>Чернетка</option>
            <option value="published" @selected(old('status', $block->status) === 'published')>Опубліковано</option>
        </select>
    </div>
</div>

<button type="submit" class="btn btn-dark">Зберегти</button>
<a href="{{ route('admin.inside-community.blocks.index') }}" class="btn btn-outline-secondary">Скасувати</a>
