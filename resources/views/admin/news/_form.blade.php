@csrf

@php
    // Вкладки основної колонки. Права колонка (публікація, теги, адреса) видна завжди.
    $tabs = [
        'uk' => 'Українська',
        'en' => 'English',
        'picture' => 'Картинка',
    ];

    // На якій вкладці поле — щоб після помилки відкрити саме її.
    $tabOf = fn (string $field) => match (true) {
        in_array($field, ['title.uk', 'text.uk'], true) => 'uk',
        in_array($field, ['title.en', 'text.en'], true) => 'en',
        str_starts_with($field, 'picture'), $field === 'big_picture' => 'picture',
        default => null,
    };
    $errorTabs = collect($errors->keys())->map($tabOf)->filter()->unique()->values();
    $status = old('status', $news->status ?: 'draft');
@endphp

<style>
    @media (min-width: 992px) {
        .news-tabs .nav-link { padding-inline: .8rem; }
    }
</style>

<div class="row g-4 align-items-start">
    {{-- Основне: тексти мовами, картинка --}}
    <div class="col-lg-7 col-xl-8" x-data="{ tab: @js($errorTabs->first() ?? 'uk') }">
        <ul class="nav nav-pills news-tabs mb-3">
            @foreach ($tabs as $key => $label)
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="tab === '{{ $key }}' && 'active'" @click="tab = '{{ $key }}'">
                        {{ $label }}
                        @if ($errorTabs->contains($key))
                            <i class="bi bi-exclamation-circle-fill text-danger ms-1" title="Є помилка"></i>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>

        @foreach (['uk' => 'UA', 'en' => 'EN'] as $lang => $code)
            <div class="card" x-show="tab === '{{ $lang }}'" x-cloak>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Заголовок ({{ $code }})@if ($lang === 'uk') <span class="text-danger">*</span>@endif</label>
                        <input type="text" name="title[{{ $lang }}]" class="form-control form-control-lg"
                            value="{{ old("title.$lang", $news->getTranslation('title', $lang, false)) }}">
                        <div class="form-text">Він же заголовок сторінки в Google і в посиланнях у месенджерах.</div>
                    </div>

                    <label class="form-label">Текст новини ({{ $code }})</label>
                    @include('admin._rich_text_field', [
                        'name' => "text[$lang]",
                        'id' => "news-text-$lang",
                        'value' => $news->getTranslation('text', $lang, false),
                        'oldKey' => "text.$lang",
                        'height' => 480,
                    ])
                </div>
            </div>
        @endforeach

        <div class="card" x-show="tab === 'picture'" x-cloak>
            <div class="card-body">
                <label class="form-label">Зображення</label>
                @if ($news->picture)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$news->picture) }}" alt="" style="max-height:200px;" class="rounded border">
                    </div>
                @endif
                <input type="file" name="picture" accept="image/*" class="form-control">
                @include('admin._image_alt', ['name' => 'picture_alt', 'attribute' => 'picture_alt', 'model' => $news])

                <div class="form-check form-switch mt-4">
                    <input type="hidden" name="big_picture" value="0">
                    <input type="checkbox" name="big_picture" value="1" class="form-check-input" id="big_picture"
                        @checked(old('big_picture', $news->big_picture))>
                    <label class="form-check-label" for="big_picture">Велика картка (big picture)</label>
                </div>
            </div>
        </div>
    </div>

    {{-- Права колонка: публікація і збереження завжди на виду --}}
    <div class="col-lg-5 col-xl-4 order-first order-lg-last">
        <div class="news-aside d-flex flex-column gap-3">
            <div class="card">
                <div class="card-header fw-semibold">Публікація</div>
                <div class="card-body">
                    <div class="btn-group w-100 mb-3" role="group" aria-label="Статус">
                        <input type="radio" class="btn-check" name="status" value="draft" id="status-draft" @checked($status === 'draft')>
                        <label class="btn btn-outline-secondary" for="status-draft">Чернетка</label>
                        <input type="radio" class="btn-check" name="status" value="published" id="status-published" @checked($status === 'published')>
                        <label class="btn btn-outline-success" for="status-published">Опубліковано</label>
                    </div>

                    <label class="form-label">Дата публікації</label>
                    <input type="datetime-local" name="published_at" class="form-control mb-3"
                        value="{{ old('published_at', optional($news->published_at ?? now())->format('Y-m-d\TH:i')) }}">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-fill">Зберегти</button>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Скасувати</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">Теги</div>
                <div class="card-body">
                    @forelse ($tags as $tag)
                        <div class="form-check">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="form-check-input" id="tag-{{ $tag->id }}"
                                @checked(collect(old('tags', $selectedTagIds))->contains($tag->id))>
                            <label class="form-check-label" for="tag-{{ $tag->id }}">{{ $tag->getTranslation('name', 'uk') }}</label>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">
                            Тегів ще немає. Додати можна в розділі
                            <a href="{{ route('admin.news-tags.index') }}">«Теги новин»</a>.
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">Адреса</div>
                <div class="card-body">
                    <label class="form-label">URL (slug)</label>
                    <div class="input-group">
                        <span class="input-group-text">/news/</span>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $news->slug) }}" placeholder="з заголовка">
                    </div>
                    <div class="form-text mb-3">Один на обидві мови, латиницею. Порожній — згенерується із заголовка. Стара адреса після зміни веде на нову (301).</div>

                    <label class="form-label">Зовнішнє посилання</label>
                    <input type="url" name="external_link" class="form-control" value="{{ old('external_link', $news->external_link) }}" placeholder="https://">
                    <div class="form-text">Якщо новина — просто посилання на іншу сторінку.</div>
                </div>
            </div>
        </div>
    </div>
</div>
