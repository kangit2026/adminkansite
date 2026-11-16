@extends('admin.layout')

@section('title', 'Головна')

@php
    // Три вкладки однієї форми. «Зберегти» зберігає всі разом.
    $tabs = [
        'seo' => 'SEO і відео',
        'banner' => 'Банер продажу',
        'about' => 'Про компанію',
    ];

    // На якій вкладці поле — щоб після помилки відкрити саме її.
    $tabOf = fn (string $field) => match (true) {
        str_starts_with($field, 'commercial_ad_') => 'banner',
        str_starts_with($field, 'motto'), str_starts_with($field, 'award') => 'about',
        default => 'seo',
    };
    $errorTabs = collect($errors->keys())->map($tabOf)->unique()->values();
@endphp

@section('content')
<h1 class="h3 mb-4">Головна (/)</h1>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="pageTabs(@js(array_keys($tabs)), @js($errorTabs->first()))">
    @include('admin._page_tabs')

    <form method="POST" action="{{ route('admin.settings.update') }}" :action="'{{ route('admin.settings.update') }}#' + tab" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card mb-4" x-show="tab === 'seo'" x-cloak>
            <div class="card-body" x-data="{ lang: 'uk' }">
                <p class="text-muted small mb-3">Як головна виглядає в Google і в посиланнях, а також відео в першому екрані.</p>

                @include('admin._lang_tabs')

                <div x-show="lang === 'uk'">
                    <div class="mb-3">
                        <label class="form-label">Meta title (UA)</label>
                        <input type="text" name="home_meta_title[uk]" maxlength="70" class="form-control"
                            value="{{ old('home_meta_title.uk', $settings->getTranslation('home_meta_title', 'uk')) }}">
                        <div class="form-text">До 70 символів. Рекомендовано для Google.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta description (UA)</label>
                        <textarea name="home_meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('home_meta_description.uk', $settings->getTranslation('home_meta_description', 'uk')) }}</textarea>
                        <div class="form-text">До 160 символів.</div>
                    </div>
                </div>

                <div x-show="lang === 'en'">
                    <div class="mb-3">
                        <label class="form-label">Meta title (EN)</label>
                        <input type="text" name="home_meta_title[en]" maxlength="70" class="form-control"
                            value="{{ old('home_meta_title.en', $settings->getTranslation('home_meta_title', 'en')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta description (EN)</label>
                        <textarea name="home_meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('home_meta_description.en', $settings->getTranslation('home_meta_description', 'en')) }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">OG-картинка (для посилань у соцмережах/месенджерах)</label>
                    @if ($settings->home_og_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$settings->home_og_image) }}" alt="" style="max-height:120px;" class="rounded border">
                        </div>
                    @endif
                    <input type="file" name="home_og_image" accept="image/*" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Відео-маніфест (хіро-блок)</label>
                    @if ($settings->home_video)
                        <div class="mb-2">
                            <video src="{{ asset('storage/'.$settings->home_video) }}" controls style="max-height:200px;" class="rounded border"></video>
                        </div>
                    @else
                        <div class="form-text mb-1">Зараз використовується дефолтний файл із фронтенду.</div>
                    @endif
                    <input type="file" name="home_video" accept="video/mp4,video/webm,video/quicktime" class="form-control">
                    <div class="form-text">MP4/WebM/MOV, до 50 МБ.</div>
                </div>
            </div>
        </div>

        <div class="card mb-4" x-show="tab === 'banner'" x-cloak>
            <div class="card-body" x-data="{ lang: 'uk' }">
                <p class="text-muted small mb-3">Банер «Продаж комерційних приміщень» — на головній і у футері сторінки /areas.</p>

                @include('admin._lang_tabs')

                <div x-show="lang === 'uk'" class="mb-3">
                    <label class="form-label">Текст банера (UA)</label>
                    <input type="text" name="commercial_ad_title[uk]" class="form-control"
                        value="{{ old('commercial_ad_title.uk', $settings->getTranslation('commercial_ad_title', 'uk')) }}">
                </div>
                <div x-show="lang === 'en'" class="mb-3">
                    <label class="form-label">Текст банера (EN)</label>
                    <input type="text" name="commercial_ad_title[en]" class="form-control"
                        value="{{ old('commercial_ad_title.en', $settings->getTranslation('commercial_ad_title', 'en')) }}">
                </div>

                <div class="mb-1">
                    <label class="form-label">Посилання</label>
                    <input type="text" name="commercial_ad_link" class="form-control"
                        value="{{ old('commercial_ad_link', $settings->commercial_ad_link) }}" placeholder="/platform">
                </div>
            </div>
        </div>

        <div class="card mb-4" x-show="tab === 'about'" x-cloak>
            <div class="card-body" x-data="{ lang: 'uk' }">
                <p class="text-muted small mb-3">Блок «Про компанію»: мотто, заголовок і чотири показники.</p>

                @include('admin._lang_tabs')

                <div x-show="lang === 'uk'">
                    <div class="mb-3">
                        <label class="form-label">Мотто (напр. «Створюй із Нами»)</label>
                        <input type="text" name="motto[uk]" class="form-control"
                            value="{{ old('motto.uk', $settings->getTranslation('motto', 'uk')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Заголовок блоку (напр. «Про компанію»)</label>
                        <input type="text" name="awards_title[uk]" class="form-control"
                            value="{{ old('awards_title.uk', $settings->getTranslation('awards_title', 'uk')) }}">
                    </div>
                </div>
                <div x-show="lang === 'en'">
                    <div class="mb-3">
                        <label class="form-label">Мотто (EN)</label>
                        <input type="text" name="motto[en]" class="form-control"
                            value="{{ old('motto.en', $settings->getTranslation('motto', 'en')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Заголовок блоку (EN)</label>
                        <input type="text" name="awards_title[en]" class="form-control"
                            value="{{ old('awards_title.en', $settings->getTranslation('awards_title', 'en')) }}">
                    </div>
                </div>

                <hr>

                @foreach (['first' => '1-й показник', 'second' => '2-й показник', 'third' => '3-й показник', 'fourth' => '4-й показник'] as $key => $label)
                    <div class="row g-3 mb-2 align-items-start">
                        <div class="col-md-3">
                            <label class="form-label">{{ $label }} — число</label>
                            <input type="number" name="award_{{ $key }}_value" class="form-control"
                                value="{{ old("award_{$key}_value", $settings->{"award_{$key}_value"}) }}" placeholder="10000">
                        </div>
                        <div class="col-md-4" x-show="lang === 'uk'">
                            <label class="form-label">Опис (UA)</label>
                            <input type="text" name="award_{{ $key }}_description[uk]" class="form-control"
                                value="{{ old("award_{$key}_description.uk", $settings->getTranslation("award_{$key}_description", 'uk')) }}"
                                placeholder="Щасливих сімей">
                        </div>
                        <div class="col-md-4" x-show="lang === 'en'">
                            <label class="form-label">Опис (EN)</label>
                            <input type="text" name="award_{{ $key }}_description[en]" class="form-control"
                                value="{{ old("award_{$key}_description.en", $settings->getTranslation("award_{$key}_description", 'en')) }}"
                                placeholder="Happy families">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-dark">Зберегти</button>
            <span class="form-text m-0">Зберігаються всі три вкладки разом.</span>
        </div>
    </form>
</div>
@endsection
