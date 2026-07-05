@csrf

<div class="card mb-4">
    <div class="card-header fw-semibold">Основне</div>
    <div class="card-body" x-data="{ lang: 'uk' }">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <button type="button" class="nav-link" :class="lang === 'uk' && 'active'" @click="lang = 'uk'">UA</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" :class="lang === 'en' && 'active'" @click="lang = 'en'">EN</button>
            </li>
        </ul>

        <div class="mb-3">
            <label class="form-label">URL (slug) — один на обидві мови, англійською</label>
            <input type="text" name="slug" class="form-control"
                value="{{ old('slug', $news->slug) }}"
                placeholder="залиште порожнім — згенерується з англійського заголовка">
            <div class="form-text">
                Змінюйте обережно: стара адреса автоматично почне 301-редиректити на нову, але краще без потреби не чіпати.
            </div>
        </div>

        <div x-show="lang === 'uk'">
            <div class="mb-3">
                <label class="form-label">Заголовок (UA)</label>
                <input type="text" name="title[uk]" class="form-control"
                    value="{{ old('title.uk', $news->getTranslation('title', 'uk')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Текст новини (UA)</label>
                <textarea name="text[uk]" rows="8" class="form-control">{{ old('text.uk', $news->getTranslation('text', 'uk')) }}</textarea>
            </div>
        </div>

        <div x-show="lang === 'en'">
            <div class="mb-3">
                <label class="form-label">Заголовок (EN)</label>
                <input type="text" name="title[en]" class="form-control"
                    value="{{ old('title.en', $news->getTranslation('title', 'en')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Текст новини (EN)</label>
                <textarea name="text[en]" rows="8" class="form-control">{{ old('text.en', $news->getTranslation('text', 'en')) }}</textarea>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Зображення</label>
            @if ($news->picture)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$news->picture) }}" alt="" style="max-height:120px;" class="rounded border">
                </div>
            @endif
            <input type="file" name="picture" accept="image/*" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Зовнішнє посилання (якщо новина — просто лінк назовні)</label>
            <input type="url" name="external_link" class="form-control" value="{{ old('external_link', $news->external_link) }}">
        </div>

        <div class="form-check form-switch mb-2">
            <input type="hidden" name="big_picture" value="0">
            <input type="checkbox" name="big_picture" value="1" class="form-check-input" id="big_picture"
                @checked(old('big_picture', $news->big_picture))>
            <label class="form-check-label" for="big_picture">Велика картка (big picture)</label>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Публікація</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="draft" @selected(old('status', $news->status) === 'draft')>Чернетка</option>
                    <option value="published" @selected(old('status', $news->status) === 'published')>Опубліковано</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Дата публікації</label>
                <input type="datetime-local" name="published_at" class="form-control"
                    value="{{ old('published_at', optional($news->published_at ?? now())->format('Y-m-d\TH:i')) }}">
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Теги</div>
    <div class="card-body">
        @forelse ($tags as $tag)
            <div class="form-check form-check-inline">
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="form-check-input" id="tag-{{ $tag->id }}"
                    @checked(collect(old('tags', $selectedTagIds))->contains($tag->id))>
                <label class="form-check-label" for="tag-{{ $tag->id }}">{{ $tag->getTranslation('name', 'uk') }}</label>
            </div>
        @empty
            <p class="text-muted mb-0">
                Тегів ще немає. Додати можна в розділі
                <a href="{{ route('admin.news-tags.index') }}">«Теги новин»</a>.
            </p>
        @endforelse
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">SEO</div>
    <div class="card-body" x-data="{ lang: 'uk' }">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <button type="button" class="nav-link" :class="lang === 'uk' && 'active'" @click="lang = 'uk'">UA</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" :class="lang === 'en' && 'active'" @click="lang = 'en'">EN</button>
            </li>
        </ul>

        <div x-show="lang === 'uk'">
            <div class="mb-3">
                <label class="form-label">Meta title (UA)</label>
                <input type="text" name="meta_title[uk]" maxlength="70" class="form-control"
                    value="{{ old('meta_title.uk', $news->getTranslation('meta_title', 'uk')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta description (UA)</label>
                <textarea name="meta_description[uk]" maxlength="160" rows="2" class="form-control">{{ old('meta_description.uk', $news->getTranslation('meta_description', 'uk')) }}</textarea>
            </div>
        </div>
        <div x-show="lang === 'en'">
            <div class="mb-3">
                <label class="form-label">Meta title (EN)</label>
                <input type="text" name="meta_title[en]" maxlength="70" class="form-control"
                    value="{{ old('meta_title.en', $news->getTranslation('meta_title', 'en')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta description (EN)</label>
                <textarea name="meta_description[en]" maxlength="160" rows="2" class="form-control">{{ old('meta_description.en', $news->getTranslation('meta_description', 'en')) }}</textarea>
            </div>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-dark">Зберегти</button>
<a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Скасувати</a>
