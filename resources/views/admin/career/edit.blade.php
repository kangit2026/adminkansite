@extends('admin.layout')

@section('title', 'Кар’єра')

@section('content')
<h1 class="h3 mb-4">Кар’єра (/career)</h1>

<div class="alert alert-info">
    Список вакансій зараз виводиться зі <strong>статичного каталогу в коді</strong>
    (<code>CareerVacanciesCatalog</code>) — як на kandevelopment.com. Редагування вакансій у адмінці додамо наступним етапом.
</div>

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

<form method="POST" action="{{ route('admin.career.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header fw-semibold">Заголовки та фільтри</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            @include('admin._lang_tabs')

            <div x-show="lang === 'uk'">
                <div class="mb-3">
                    <label class="form-label">Заголовок сторінки (UA)</label>
                    <input type="text" name="heading[uk]" class="form-control" value="{{ old('heading.uk', $career->getTranslation('heading', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Підпис «місто» (UA)</label>
                    <input type="text" name="city_label[uk]" class="form-control" value="{{ old('city_label.uk', $career->getTranslation('city_label', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Placeholder фільтра категорій (UA)</label>
                    <input type="text" name="all_categories_label[uk]" class="form-control" value="{{ old('all_categories_label.uk', $career->getTranslation('all_categories_label', 'uk')) }}">
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Page heading (EN)</label>
                    <input type="text" name="heading[en]" class="form-control" value="{{ old('heading.en', $career->getTranslation('heading', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">City label (EN)</label>
                    <input type="text" name="city_label[en]" class="form-control" value="{{ old('city_label.en', $career->getTranslation('city_label', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Categories filter placeholder (EN)</label>
                    <input type="text" name="all_categories_label[en]" class="form-control" value="{{ old('all_categories_label.en', $career->getTranslation('all_categories_label', 'en')) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Вступний текст (над таблицею вакансій)</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">UA</label>
                @include('admin._rich_text_field', [
                    'name' => 'intro_text[uk]',
                    'id' => 'career-intro-uk',
                    'value' => $career->getTranslation('intro_text', 'uk'),
                    'oldKey' => 'intro_text.uk',
                ])
            </div>
            <div class="mb-1">
                <label class="form-label">EN</label>
                @include('admin._rich_text_field', [
                    'name' => 'intro_text[en]',
                    'id' => 'career-intro-en',
                    'value' => $career->getTranslation('intro_text', 'en'),
                    'oldKey' => 'intro_text.en',
                ])
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
                    <input type="text" name="meta_title[uk]" maxlength="70" class="form-control" value="{{ old('meta_title.uk', $career->getTranslation('meta_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (UA)</label>
                    <textarea name="meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.uk', $career->getTranslation('meta_description', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Meta title (EN)</label>
                    <input type="text" name="meta_title[en]" maxlength="70" class="form-control" value="{{ old('meta_title.en', $career->getTranslation('meta_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (EN)</label>
                    <textarea name="meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.en', $career->getTranslation('meta_description', 'en')) }}</textarea>
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label">OG-картинка</label>
                @if ($career->og_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$career->og_image) }}" alt="" style="max-height:120px;" class="rounded border">
                    </div>
                @endif
                <input type="file" name="og_image" accept="image/*" class="form-control">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-dark">Зберегти</button>
</form>
@endsection
