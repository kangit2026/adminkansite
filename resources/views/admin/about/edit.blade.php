@extends('admin.layout')

@section('title', 'Про компанію')

@section('content')
<h1 class="h3 mb-4">Сторінка "Про компанію" (/about)</h1>

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

<form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header fw-semibold">Хіро-блок</div>
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
                    <label class="form-label">Мотто (напр. "Створюй із Нами")</label>
                    <input type="text" name="motto[uk]" class="form-control"
                        value="{{ old('motto.uk', $about->getTranslation('motto', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Заголовок (напр. "Про компанію")</label>
                    <input type="text" name="heading[uk]" class="form-control"
                        value="{{ old('heading.uk', $about->getTranslation('heading', 'uk')) }}">
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Мотто (EN)</label>
                    <input type="text" name="motto[en]" class="form-control"
                        value="{{ old('motto.en', $about->getTranslation('motto', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Заголовок (EN)</label>
                    <input type="text" name="heading[en]" class="form-control"
                        value="{{ old('heading.en', $about->getTranslation('heading', 'en')) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Вступний текст</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Перший абзац (UA)</label>
                @include('admin._rich_text_field', [
                    'name' => 'history[uk]',
                    'id' => 'about-history-uk',
                    'value' => $about->getTranslation('history', 'uk'),
                    'oldKey' => 'history.uk',
                    'height' => 280,
                ])
            </div>
            <div class="mb-3">
                <label class="form-label">Другий абзац (UA)</label>
                @include('admin._rich_text_field', [
                    'name' => 'specialists[uk]',
                    'id' => 'about-specialists-uk',
                    'value' => $about->getTranslation('specialists', 'uk'),
                    'oldKey' => 'specialists.uk',
                    'height' => 220,
                ])
            </div>
            <hr class="my-4">
            <div class="mb-3">
                <label class="form-label">Перший абзац (EN)</label>
                @include('admin._rich_text_field', [
                    'name' => 'history[en]',
                    'id' => 'about-history-en',
                    'value' => $about->getTranslation('history', 'en'),
                    'oldKey' => 'history.en',
                    'height' => 280,
                ])
            </div>
            <div class="mb-1">
                <label class="form-label">Другий абзац (EN)</label>
                @include('admin._rich_text_field', [
                    'name' => 'specialists[en]',
                    'id' => 'about-specialists-en',
                    'value' => $about->getTranslation('specialists', 'en'),
                    'oldKey' => 'specialists.en',
                    'height' => 220,
                ])
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Блок "Сьогодні"</div>
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
                    <label class="form-label">Заголовок (UA)</label>
                    <input type="text" name="today_title[uk]" class="form-control"
                        value="{{ old('today_title.uk', $about->getTranslation('today_title', 'uk')) }}">
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Заголовок (EN)</label>
                    <input type="text" name="today_title[en]" class="form-control"
                        value="{{ old('today_title.en', $about->getTranslation('today_title', 'en')) }}">
                </div>
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <label class="form-label">Опис (UA)</label>
                @include('admin._rich_text_field', [
                    'name' => 'today_description[uk]',
                    'id' => 'about-today-uk',
                    'value' => $about->getTranslation('today_description', 'uk'),
                    'oldKey' => 'today_description.uk',
                    'height' => 220,
                ])
            </div>
            <div class="mb-1">
                <label class="form-label">Опис (EN)</label>
                @include('admin._rich_text_field', [
                    'name' => 'today_description[en]',
                    'id' => 'about-today-en',
                    'value' => $about->getTranslation('today_description', 'en'),
                    'oldKey' => 'today_description.en',
                    'height' => 220,
                ])
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Блок "Переваги"</div>
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
                    <label class="form-label">Заголовок (UA)</label>
                    <input type="text" name="merit_title[uk]" class="form-control"
                        value="{{ old('merit_title.uk', $about->getTranslation('merit_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Опис (UA)</label>
                    <textarea name="merit_description[uk]" rows="3" class="form-control">{{ old('merit_description.uk', $about->getTranslation('merit_description', 'uk')) }}</textarea>
                </div>
                <div class="row g-3">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-6">
                            <label class="form-label">Пункт {{ $i }} (UA)</label>
                            <input type="text" name="merit_list_{{ $i }}[uk]" class="form-control"
                                value="{{ old("merit_list_{$i}.uk", $about->getTranslation("merit_list_{$i}", 'uk')) }}">
                        </div>
                    @endfor
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Заголовок (EN)</label>
                    <input type="text" name="merit_title[en]" class="form-control"
                        value="{{ old('merit_title.en', $about->getTranslation('merit_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Опис (EN)</label>
                    <textarea name="merit_description[en]" rows="3" class="form-control">{{ old('merit_description.en', $about->getTranslation('merit_description', 'en')) }}</textarea>
                </div>
                <div class="row g-3">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-6">
                            <label class="form-label">Пункт {{ $i }} (EN)</label>
                            <input type="text" name="merit_list_{{ $i }}[en]" class="form-control"
                                value="{{ old("merit_list_{$i}.en", $about->getTranslation("merit_list_{$i}", 'en')) }}">
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Цитата-плашка "Головні досягнення..."</div>
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
                <label class="form-label">Текст (UA)</label>
                <textarea name="achievements_text[uk]" rows="3" maxlength="500" class="form-control">{{ old('achievements_text.uk', $about->getTranslation('achievements_text', 'uk')) }}</textarea>
            </div>
            <div x-show="lang === 'en'">
                <label class="form-label">Текст (EN)</label>
                <textarea name="achievements_text[en]" rows="3" maxlength="500" class="form-control">{{ old('achievements_text.en', $about->getTranslation('achievements_text', 'en')) }}</textarea>
            </div>
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
                        value="{{ old('meta_title.uk', $about->getTranslation('meta_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (UA)</label>
                    <textarea name="meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.uk', $about->getTranslation('meta_description', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Meta title (EN)</label>
                    <input type="text" name="meta_title[en]" maxlength="70" class="form-control"
                        value="{{ old('meta_title.en', $about->getTranslation('meta_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (EN)</label>
                    <textarea name="meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.en', $about->getTranslation('meta_description', 'en')) }}</textarea>
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label">OG-картинка (для посилань у соцмережах/месенджерах)</label>
                @if ($about->og_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$about->og_image) }}" alt="" style="max-height:120px;" class="rounded border">
                    </div>
                @endif
                <input type="file" name="og_image" accept="image/*" class="form-control">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-dark">Зберегти</button>
</form>
@endsection
