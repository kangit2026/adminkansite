@extends('admin.layout')

@section('title', 'Футер')

@php
    // Чотири вкладки однієї форми. «Зберегти» зберігає всі разом.
    $tabs = [
        'contacts' => 'Контакти',
        'socials' => 'Соцмережі',
        'banner' => 'Банер ділянок',
        'copyright' => 'Копірайт',
    ];

    // На якій вкладці поле — щоб після помилки відкрити саме її.
    $tabOf = fn (string $field) => match (true) {
        str_starts_with($field, 'social_') => 'socials',
        str_starts_with($field, 'project_ad_') => 'banner',
        str_starts_with($field, 'copyright_') => 'copyright',
        default => 'contacts',
    };
    $errorTabs = collect($errors->keys())->map($tabOf)->unique()->values();
@endphp

@section('content')
<h1 class="h3 mb-4">Футер</h1>

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

    <form method="POST" action="{{ route('admin.footer.update') }}" :action="'{{ route('admin.footer.update') }}#' + tab">
        @csrf
        @method('PUT')

        <div class="card mb-4" x-show="tab === 'contacts'" x-cloak>
            <div class="card-body">
                <p class="text-muted small mb-3">Телефон, e-mail і чатбот — у шапці та футері на всіх сторінках.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Телефон</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings->phone) }}" placeholder="+38 (044) 247 57 48">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $settings->email) }}" placeholder="office@kandevelopment.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Посилання на Telegram-чатбот</label>
                        <input type="url" name="telegram_chat_link" class="form-control" value="{{ old('telegram_chat_link', $settings->telegram_chat_link) }}" placeholder="https://t.me/kan_chatbot">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4" x-show="tab === 'socials'" x-cloak>
            <div class="card-body">
                <p class="text-muted small mb-3">Іконки соцмереж у футері. Порожнє поле — іконки немає.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Instagram</label>
                        <input type="url" name="social_instagram" class="form-control" value="{{ old('social_instagram', $settings->social_instagram) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">YouTube</label>
                        <input type="url" name="social_youtube" class="form-control" value="{{ old('social_youtube', $settings->social_youtube) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Facebook</label>
                        <input type="url" name="social_facebook" class="form-control" value="{{ old('social_facebook', $settings->social_facebook) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pinterest</label>
                        <input type="url" name="social_pinterest" class="form-control" value="{{ old('social_pinterest', $settings->social_pinterest) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telegram (канал)</label>
                        <input type="url" name="social_telegram" class="form-control" value="{{ old('social_telegram', $settings->social_telegram) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4" x-show="tab === 'banner'" x-cloak>
            <div class="card-body" x-data="{ lang: 'uk' }">
                <p class="text-muted small mb-3">Банер «Шукаємо ділянки для нових проектів» — у футері на більшості сторінок.</p>

                @include('admin._lang_tabs')

                <div x-show="lang === 'uk'" class="mb-3">
                    <label class="form-label">Текст банера (UA)</label>
                    <input type="text" name="project_ad_title[uk]" class="form-control"
                        value="{{ old('project_ad_title.uk', $settings->getTranslation('project_ad_title', 'uk')) }}">
                </div>
                <div x-show="lang === 'en'" class="mb-3">
                    <label class="form-label">Текст банера (EN)</label>
                    <input type="text" name="project_ad_title[en]" class="form-control"
                        value="{{ old('project_ad_title.en', $settings->getTranslation('project_ad_title', 'en')) }}">
                </div>

                <div class="mb-1">
                    <label class="form-label">Посилання</label>
                    <input type="text" name="project_ad_link" class="form-control"
                        value="{{ old('project_ad_link', $settings->project_ad_link) }}" placeholder="/areas">
                </div>
            </div>
        </div>

        <div class="card mb-4" x-show="tab === 'copyright'" x-cloak>
            <div class="card-body" x-data="{ lang: 'uk' }">
                <p class="text-muted small mb-3">Рядок копірайту внизу футера.</p>

                <div class="mb-3" style="max-width: 160px;">
                    <label class="form-label">Рік</label>
                    <input type="number" name="copyright_year" class="form-control" value="{{ old('copyright_year', $settings->copyright_year) }}">
                </div>

                @include('admin._lang_tabs')

                <div x-show="lang === 'uk'">
                    <label class="form-label">Текст копірайту (UA)</label>
                    <input type="text" name="copyright_text[uk]" class="form-control"
                        value="{{ old('copyright_text.uk', $settings->getTranslation('copyright_text', 'uk')) }}"
                        placeholder="Всi права захищено. ТОВ «КАН ДЕВЕЛОПМЕНТ»">
                </div>
                <div x-show="lang === 'en'">
                    <label class="form-label">Текст копірайту (EN)</label>
                    <input type="text" name="copyright_text[en]" class="form-control"
                        value="{{ old('copyright_text.en', $settings->getTranslation('copyright_text', 'en')) }}"
                        placeholder="All right reserved «KAN DEVELOPMENT» LLC">
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-dark">Зберегти</button>
            <span class="form-text m-0">Зберігаються всі чотири вкладки разом.</span>
        </div>
    </form>
</div>
@endsection
