@extends('admin.layout')

@section('title', 'Контакти')

@php
    // Три вкладки однієї форми. «Зберегти» зберігає всі разом.
    $tabs = [
        'form' => 'Форма',
        'map' => 'Карта',
        'seo' => 'SEO',
    ];

    // На якій вкладці поле — щоб після помилки відкрити саме її.
    $tabOf = fn (string $field) => match (true) {
        str_starts_with($field, 'map_') => 'map',
        str_starts_with($field, 'meta_'), $field === 'og_image' => 'seo',
        default => 'form',
    };
    $errorTabs = collect($errors->keys())->map($tabOf)->unique()->values();
@endphp

@section('content')
<h1 class="h3 mb-4">Контакти (/contacts)</h1>

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

    <form method="POST" action="{{ route('admin.contacts.update') }}" :action="'{{ route('admin.contacts.update') }}#' + tab" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card mb-4" x-show="tab === 'form'" x-cloak>
            <div class="card-body" x-data="{ lang: 'uk' }">
                <p class="text-muted small mb-3">Форма зворотного зв’язку на сторінці /contacts: заголовок, підказки в полях, кнопки й повідомлення після відправки.</p>

                @include('admin._lang_tabs')

                <div x-show="lang === 'uk'">
                    <div class="mb-3">
                        <label class="form-label">Заголовок (UA)</label>
                        <input type="text" name="heading[uk]" class="form-control" value="{{ old('heading.uk', $contacts->getTranslation('heading', 'uk')) }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Placeholder «Ім’я» (UA)</label>
                            <input type="text" name="name_label[uk]" class="form-control" value="{{ old('name_label.uk', $contacts->getTranslation('name_label', 'uk')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Placeholder «Телефон» (UA)</label>
                            <input type="text" name="phone_label[uk]" class="form-control" value="{{ old('phone_label.uk', $contacts->getTranslation('phone_label', 'uk')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Placeholder «Email» (UA)</label>
                            <input type="text" name="email_label[uk]" class="form-control" value="{{ old('email_label.uk', $contacts->getTranslation('email_label', 'uk')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Placeholder «Повідомлення» (UA)</label>
                            <input type="text" name="message_label[uk]" class="form-control" value="{{ old('message_label.uk', $contacts->getTranslation('message_label', 'uk')) }}">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Кнопка «Надіслати» (UA)</label>
                        <input type="text" name="send_label[uk]" class="form-control" value="{{ old('send_label.uk', $contacts->getTranslation('send_label', 'uk')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Повідомлення успіху (UA)</label>
                        <textarea name="success_message[uk]" rows="2" class="form-control">{{ old('success_message.uk', $contacts->getTranslation('success_message', 'uk')) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Повідомлення помилки (UA)</label>
                        <textarea name="error_message[uk]" rows="2" class="form-control">{{ old('error_message.uk', $contacts->getTranslation('error_message', 'uk')) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Кнопка «Надіслати знову» (UA)</label>
                        <input type="text" name="send_again_label[uk]" class="form-control" value="{{ old('send_again_label.uk', $contacts->getTranslation('send_again_label', 'uk')) }}">
                    </div>
                </div>

                <div x-show="lang === 'en'">
                    <div class="mb-3">
                        <label class="form-label">Heading (EN)</label>
                        <input type="text" name="heading[en]" class="form-control" value="{{ old('heading.en', $contacts->getTranslation('heading', 'en')) }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name placeholder (EN)</label>
                            <input type="text" name="name_label[en]" class="form-control" value="{{ old('name_label.en', $contacts->getTranslation('name_label', 'en')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone placeholder (EN)</label>
                            <input type="text" name="phone_label[en]" class="form-control" value="{{ old('phone_label.en', $contacts->getTranslation('phone_label', 'en')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email placeholder (EN)</label>
                            <input type="text" name="email_label[en]" class="form-control" value="{{ old('email_label.en', $contacts->getTranslation('email_label', 'en')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Message placeholder (EN)</label>
                            <input type="text" name="message_label[en]" class="form-control" value="{{ old('message_label.en', $contacts->getTranslation('message_label', 'en')) }}">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Send button (EN)</label>
                        <input type="text" name="send_label[en]" class="form-control" value="{{ old('send_label.en', $contacts->getTranslation('send_label', 'en')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Success message (EN)</label>
                        <textarea name="success_message[en]" rows="2" class="form-control">{{ old('success_message.en', $contacts->getTranslation('success_message', 'en')) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Error message (EN)</label>
                        <textarea name="error_message[en]" rows="2" class="form-control">{{ old('error_message.en', $contacts->getTranslation('error_message', 'en')) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Send again button (EN)</label>
                        <input type="text" name="send_again_label[en]" class="form-control" value="{{ old('send_again_label.en', $contacts->getTranslation('send_again_label', 'en')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4" x-show="tab === 'map'" x-cloak>
            <div class="card-body">
                <p class="text-muted small mb-3">Карта HERE Maps поруч із формою: точка офісу й масштаб.</p>

                <div class="form-check mb-3">
                    <input type="hidden" name="map_enabled" value="0">
                    <input class="form-check-input" type="checkbox" name="map_enabled" value="1" id="map_enabled"
                        @checked(old('map_enabled', $contacts->map_enabled))>
                    <label class="form-check-label" for="map_enabled">Показувати карту поруч із формою</label>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Широта (lat)</label>
                        <input type="text" name="map_lat" class="form-control" value="{{ old('map_lat', $contacts->map_lat) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Довгота (lng)</label>
                        <input type="text" name="map_lng" class="form-control" value="{{ old('map_lng', $contacts->map_lng) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Zoom</label>
                        <input type="number" name="map_zoom" min="1" max="21" class="form-control" value="{{ old('map_zoom', $contacts->map_zoom ?? 16) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4" x-show="tab === 'seo'" x-cloak>
            <div class="card-body" x-data="{ lang: 'uk' }">
                <p class="text-muted small mb-3">Як сторінка /contacts виглядає в Google і в посиланнях у месенджерах.</p>

                @include('admin._lang_tabs')

                <div x-show="lang === 'uk'">
                    <div class="mb-3">
                        <label class="form-label">Meta title (UA)</label>
                        <input type="text" name="meta_title[uk]" maxlength="70" class="form-control" value="{{ old('meta_title.uk', $contacts->getTranslation('meta_title', 'uk')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta description (UA)</label>
                        <textarea name="meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.uk', $contacts->getTranslation('meta_description', 'uk')) }}</textarea>
                    </div>
                </div>
                <div x-show="lang === 'en'">
                    <div class="mb-3">
                        <label class="form-label">Meta title (EN)</label>
                        <input type="text" name="meta_title[en]" maxlength="70" class="form-control" value="{{ old('meta_title.en', $contacts->getTranslation('meta_title', 'en')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta description (EN)</label>
                        <textarea name="meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.en', $contacts->getTranslation('meta_description', 'en')) }}</textarea>
                    </div>
                </div>

                <div class="mb-1">
                    <label class="form-label">OG-картинка</label>
                    @if ($contacts->og_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$contacts->og_image) }}" alt="" style="max-height:120px;" class="rounded border">
                        </div>
                    @endif
                    <input type="file" name="og_image" accept="image/*" class="form-control">
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-dark">Зберегти</button>
            <span class="form-text m-0">Зберігаються всі три вкладки разом.</span>
        </div>
    </form>
</div>
@endsection
