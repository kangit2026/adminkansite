@extends('admin.layout')

@section('title', 'Inside Community — сторінка')

@section('content')
<h1 class="h3 mb-4">Inside Community (/inside_community)</h1>

@include('admin._inside_community_tabs')

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

<form method="POST" action="{{ route('admin.inside-community.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header fw-semibold">Хіро-блок (відео та зображення)</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Відео (MP4/WebM)</label>
                @if ($page->hero_video)
                    <div class="form-text mb-2">Поточне: {{ basename($page->hero_video) }}</div>
                @else
                    <div class="form-text mb-2">За замовчуванням: kan_inside.mp4 з фронтенду</div>
                @endif
                <input type="file" name="hero_video" accept="video/mp4,video/webm" class="form-control">
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Зображення поверх відео (UA — PNG)</label>
                    @if ($page->hero_image_uk)
                        <div class="mb-2"><img src="{{ asset('storage/'.$page->hero_image_uk) }}" alt="" style="max-height:80px;" class="rounded border"></div>
                    @endif
                    <input type="file" name="hero_image_uk" accept="image/*" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Зображення поверх відео (EN — SVG/PNG)</label>
                    @if ($page->hero_image_en)
                        <div class="mb-2"><img src="{{ asset('storage/'.$page->hero_image_en) }}" alt="" style="max-height:80px;" class="rounded border"></div>
                    @endif
                    <input type="file" name="hero_image_en" accept="image/*" class="form-control">
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Логотип комплексу 1</label>
                    @if ($page->complex_logo_1)
                        <div class="mb-2"><img src="{{ asset('storage/'.$page->complex_logo_1) }}" alt="" style="max-height:60px;" class="rounded border"></div>
                    @endif
                    <input type="file" name="complex_logo_1" accept="image/*" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Логотип комплексу 2</label>
                    @if ($page->complex_logo_2)
                        <div class="mb-2"><img src="{{ asset('storage/'.$page->complex_logo_2) }}" alt="" style="max-height:60px;" class="rounded border"></div>
                    @endif
                    <input type="file" name="complex_logo_2" accept="image/*" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Блок «Жити — Бути — Робити»</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            @include('admin._lang_tabs')

            <div x-show="lang === 'uk'">
                <div class="mb-3">
                    <label class="form-label">Заголовок (UA)</label>
                    <textarea name="hero_title[uk]" rows="3" class="form-control">{{ old('hero_title.uk', $page->getTranslation('hero_title', 'uk')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Підзаголовок (UA)</label>
                    <textarea name="hero_subtitle[uk]" rows="2" class="form-control">{{ old('hero_subtitle.uk', $page->getTranslation('hero_subtitle', 'uk')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Абзац 1 (UA)</label>
                    <textarea name="hero_paragraph_1[uk]" rows="4" class="form-control">{{ old('hero_paragraph_1.uk', $page->getTranslation('hero_paragraph_1', 'uk')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Абзац 2 (UA)</label>
                    <textarea name="hero_paragraph_2[uk]" rows="2" class="form-control">{{ old('hero_paragraph_2.uk', $page->getTranslation('hero_paragraph_2', 'uk')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Абзац 3 (UA)</label>
                    <textarea name="hero_paragraph_3[uk]" rows="3" class="form-control">{{ old('hero_paragraph_3.uk', $page->getTranslation('hero_paragraph_3', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Заголовок (EN)</label>
                    <textarea name="hero_title[en]" rows="3" class="form-control">{{ old('hero_title.en', $page->getTranslation('hero_title', 'en')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Підзаголовок (EN)</label>
                    <textarea name="hero_subtitle[en]" rows="2" class="form-control">{{ old('hero_subtitle.en', $page->getTranslation('hero_subtitle', 'en')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Абзац 1 (EN)</label>
                    <textarea name="hero_paragraph_1[en]" rows="4" class="form-control">{{ old('hero_paragraph_1.en', $page->getTranslation('hero_paragraph_1', 'en')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Абзац 2 (EN)</label>
                    <textarea name="hero_paragraph_2[en]" rows="2" class="form-control">{{ old('hero_paragraph_2.en', $page->getTranslation('hero_paragraph_2', 'en')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Абзац 3 (EN)</label>
                    <textarea name="hero_paragraph_3[en]" rows="3" class="form-control">{{ old('hero_paragraph_3.en', $page->getTranslation('hero_paragraph_3', 'en')) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">10 принципів — заголовок секції</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            @include('admin._lang_tabs')

            <div x-show="lang === 'uk'">
                <div class="mb-3">
                    <label class="form-label">Заголовок (UA)</label>
                    <input type="text" name="philosophy_title[uk]" class="form-control"
                        value="{{ old('philosophy_title.uk', $page->getTranslation('philosophy_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Підзаголовок (UA)</label>
                    <input type="text" name="philosophy_subtitle[uk]" class="form-control"
                        value="{{ old('philosophy_subtitle.uk', $page->getTranslation('philosophy_subtitle', 'uk')) }}">
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Заголовок (EN)</label>
                    <input type="text" name="philosophy_title[en]" class="form-control"
                        value="{{ old('philosophy_title.en', $page->getTranslation('philosophy_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Підзаголовок (EN)</label>
                    <input type="text" name="philosophy_subtitle[en]" class="form-control"
                        value="{{ old('philosophy_subtitle.en', $page->getTranslation('philosophy_subtitle', 'en')) }}">
                </div>
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
                    <input type="text" name="meta_title[uk]" maxlength="70" class="form-control"
                        value="{{ old('meta_title.uk', $page->getTranslation('meta_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (UA)</label>
                    <textarea name="meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.uk', $page->getTranslation('meta_description', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Meta title (EN)</label>
                    <input type="text" name="meta_title[en]" maxlength="70" class="form-control"
                        value="{{ old('meta_title.en', $page->getTranslation('meta_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (EN)</label>
                    <textarea name="meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('meta_description.en', $page->getTranslation('meta_description', 'en')) }}</textarea>
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label">OG-картинка</label>
                @if ($page->og_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$page->og_image) }}" alt="" style="max-height:120px;" class="rounded border">
                    </div>
                @endif
                <input type="file" name="og_image" accept="image/*" class="form-control">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-dark">Зберегти</button>
</form>
@endsection
