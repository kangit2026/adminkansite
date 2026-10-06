@extends('admin.layout')

@section('title', 'KAN Market')

@section('content')
<h1 class="h3 mb-4">KAN Market (/market)</h1>

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

@php
    $prosItems = $market->pros_items ?? ['uk' => [], 'en' => []];
    $prosUk = implode("\n", $prosItems['uk'] ?? []);
    $prosEn = implode("\n", $prosItems['en'] ?? []);
@endphp

<form method="POST" action="{{ route('admin.market.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header fw-semibold">Хіро-блок</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Фонове зображення</label>
                @if ($market->hero_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$market->hero_image) }}" alt="" style="max-height:120px;" class="rounded border">
                    </div>
                @else
                    <div class="form-text mb-2">За замовчуванням: bg.png з фронтенду</div>
                @endif
                <input type="file" name="hero_image" accept="image/*" class="form-control">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Тексти сторінки</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            @include('admin._lang_tabs')

            <div x-show="lang === 'uk'">
                <div class="mb-3">
                    <label class="form-label">Бейдж (UA)</label>
                    <input type="text" name="badge[uk]" class="form-control" value="{{ old('badge.uk', $market->getTranslation('badge', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Заголовок (UA)</label>
                    <input type="text" name="title[uk]" class="form-control" value="{{ old('title.uk', $market->getTranslation('title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Опис (UA)</label>
                    <textarea name="sell_text[uk]" rows="3" class="form-control">{{ old('sell_text.uk', $market->getTranslation('sell_text', 'uk')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Питання в блоці контакту (UA)</label>
                    <input type="text" name="question[uk]" class="form-control" value="{{ old('question.uk', $market->getTranslation('question', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Відповідь / заклик (UA)</label>
                    <input type="text" name="answer[uk]" class="form-control" value="{{ old('answer.uk', $market->getTranslation('answer', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Підпис до телефону (UA)</label>
                    <input type="text" name="contact_label[uk]" class="form-control" value="{{ old('contact_label.uk', $market->getTranslation('contact_label', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Заголовок списку переваг (UA)</label>
                    <input type="text" name="pros_title[uk]" class="form-control" value="{{ old('pros_title.uk', $market->getTranslation('pros_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Переваги — один пункт на рядок (UA)</label>
                    <textarea name="pros_items_uk" rows="8" class="form-control">{{ old('pros_items_uk', $prosUk) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Фінальний текст (UA)</label>
                    <textarea name="trust_text[uk]" rows="2" class="form-control">{{ old('trust_text.uk', $market->getTranslation('trust_text', 'uk')) }}</textarea>
                </div>
            </div>

            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Badge (EN)</label>
                    <input type="text" name="badge[en]" class="form-control" value="{{ old('badge.en', $market->getTranslation('badge', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Title (EN)</label>
                    <input type="text" name="title[en]" class="form-control" value="{{ old('title.en', $market->getTranslation('title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description (EN)</label>
                    <textarea name="sell_text[en]" rows="3" class="form-control">{{ old('sell_text.en', $market->getTranslation('sell_text', 'en')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact question (EN)</label>
                    <input type="text" name="question[en]" class="form-control" value="{{ old('question.en', $market->getTranslation('question', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact answer (EN)</label>
                    <input type="text" name="answer[en]" class="form-control" value="{{ old('answer.en', $market->getTranslation('answer', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone label (EN)</label>
                    <input type="text" name="contact_label[en]" class="form-control" value="{{ old('contact_label.en', $market->getTranslation('contact_label', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pros title (EN)</label>
                    <input type="text" name="pros_title[en]" class="form-control" value="{{ old('pros_title.en', $market->getTranslation('pros_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pros — one item per line (EN)</label>
                    <textarea name="pros_items_en" rows="8" class="form-control">{{ old('pros_items_en', $prosEn) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Closing text (EN)</label>
                    <textarea name="trust_text[en]" rows="2" class="form-control">{{ old('trust_text.en', $market->getTranslation('trust_text', 'en')) }}</textarea>
                </div>
            </div>

            <div class="mb-0 mt-3">
                <label class="form-label">Телефон відділу продажу</label>
                <input type="text" name="phone" class="form-control" style="max-width:320px;" value="{{ old('phone', $market->phone) }}" placeholder="+38 (067) 579-98-63">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">SEO</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            @include('admin._lang_tabs')

            <div x-show="lang === 'uk'">
                <div class="mb-3">
                    <label class="form-label">Meta title (UA)</label>
                    <input type="text" name="meta_title[uk]" maxlength="70" class="form-control" value="{{ old('meta_title.uk', $market->getTranslation('meta_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (UA)</label>
                    <textarea name="meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.uk', $market->getTranslation('meta_description', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Meta title (EN)</label>
                    <input type="text" name="meta_title[en]" maxlength="70" class="form-control" value="{{ old('meta_title.en', $market->getTranslation('meta_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (EN)</label>
                    <textarea name="meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.en', $market->getTranslation('meta_description', 'en')) }}</textarea>
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label">OG-картинка</label>
                @if ($market->og_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$market->og_image) }}" alt="" style="max-height:120px;" class="rounded border">
                    </div>
                @endif
                <input type="file" name="og_image" accept="image/*" class="form-control">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-dark">Зберегти</button>
</form>
@endsection
