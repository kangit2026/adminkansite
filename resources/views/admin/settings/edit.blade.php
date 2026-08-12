@extends('admin.layout')

@section('title', 'Налаштування сайту')

@section('content')
<h1 class="h3 mb-4">Налаштування сайту</h1>

@include('admin._page_tabs')

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

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header fw-semibold">Головна сторінка — SEO</div>
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
                <label class="form-label">Відео-маніфест (хіро-блок на головній)</label>
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

    <div class="card mb-4">
        <div class="card-header fw-semibold">Банер "Продаж комерційних приміщень" (головна + футер на /areas)</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="lang === 'uk' && 'active'" @click="lang = 'uk'">UA</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="lang === 'en' && 'active'" @click="lang = 'en'">EN</button>
                </li>
            </ul>

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

    <div class="card mb-4">
        <div class="card-header fw-semibold">Банер "Шукаємо ділянки для нових проектів" (футер на решті сторінок)</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="lang === 'uk' && 'active'" @click="lang = 'uk'">UA</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="lang === 'en' && 'active'" @click="lang = 'en'">EN</button>
                </li>
            </ul>

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

    <div class="card mb-4">
        <div class="card-header fw-semibold">Список новин (/news) — SEO</div>
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
                    <input type="text" name="news_meta_title[uk]" maxlength="70" class="form-control"
                        value="{{ old('news_meta_title.uk', $settings->getTranslation('news_meta_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (UA)</label>
                    <textarea name="news_meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('news_meta_description.uk', $settings->getTranslation('news_meta_description', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Meta title (EN)</label>
                    <input type="text" name="news_meta_title[en]" maxlength="70" class="form-control"
                        value="{{ old('news_meta_title.en', $settings->getTranslation('news_meta_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (EN)</label>
                    <textarea name="news_meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('news_meta_description.en', $settings->getTranslation('news_meta_description', 'en')) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Список проєктів (/projects) — SEO</div>
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
                    <input type="text" name="projects_meta_title[uk]" maxlength="70" class="form-control"
                        value="{{ old('projects_meta_title.uk', $settings->getTranslation('projects_meta_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (UA)</label>
                    <textarea name="projects_meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('projects_meta_description.uk', $settings->getTranslation('projects_meta_description', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Meta title (EN)</label>
                    <input type="text" name="projects_meta_title[en]" maxlength="70" class="form-control"
                        value="{{ old('projects_meta_title.en', $settings->getTranslation('projects_meta_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (EN)</label>
                    <textarea name="projects_meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('projects_meta_description.en', $settings->getTranslation('projects_meta_description', 'en')) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Список послуг (/services) — SEO</div>
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
                    <input type="text" name="services_meta_title[uk]" maxlength="70" class="form-control"
                        value="{{ old('services_meta_title.uk', $settings->getTranslation('services_meta_title', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (UA)</label>
                    <textarea name="services_meta_description[uk]" maxlength="160" rows="3" class="form-control">{{ old('services_meta_description.uk', $settings->getTranslation('services_meta_description', 'uk')) }}</textarea>
                </div>
            </div>
            <div x-show="lang === 'en'">
                <div class="mb-3">
                    <label class="form-label">Meta title (EN)</label>
                    <input type="text" name="services_meta_title[en]" maxlength="70" class="form-control"
                        value="{{ old('services_meta_title.en', $settings->getTranslation('services_meta_title', 'en')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta description (EN)</label>
                    <textarea name="services_meta_description[en]" maxlength="160" rows="3" class="form-control">{{ old('services_meta_description.en', $settings->getTranslation('services_meta_description', 'en')) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Головна сторінка — блок "Про компанію" (мотто + статистика)</div>
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
                        value="{{ old('motto.uk', $settings->getTranslation('motto', 'uk')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Заголовок блоку (напр. "Про компанію")</label>
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

    <div class="card mb-4">
        <div class="card-header fw-semibold">Контакти (футер на всіх сторінках)</div>
        <div class="card-body">
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

    <div class="card mb-4">
        <div class="card-header fw-semibold">Соцмережі</div>
        <div class="card-body">
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

    <div class="card mb-4">
        <div class="card-header fw-semibold">Футер — копірайт</div>
        <div class="card-body" x-data="{ lang: 'uk' }">
            <div class="mb-3" style="max-width: 160px;">
                <label class="form-label">Рік</label>
                <input type="number" name="copyright_year" class="form-control" value="{{ old('copyright_year', $settings->copyright_year) }}">
            </div>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="lang === 'uk' && 'active'" @click="lang = 'uk'">UA</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="lang === 'en' && 'active'" @click="lang = 'en'">EN</button>
                </li>
            </ul>

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

    <button type="submit" class="btn btn-dark">Зберегти</button>
</form>
@endsection
