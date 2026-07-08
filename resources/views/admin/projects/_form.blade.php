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
                value="{{ old('slug', $project->slug) }}"
                placeholder="залиште порожнім — згенерується з англійської назви">
            <div class="form-text">
                Змінюйте обережно: стара адреса автоматично почне 301-редиректити на нову, але краще без потреби не чіпати.
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Категорія</label>
            <select name="category_id" class="form-select">
                <option value="">— без категорії —</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $project->category_id) == $category->id)>
                        {{ $category->getTranslation('name', 'uk') }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">
                Створити нову можна в розділі <a href="{{ route('admin.project-categories.index') }}" target="_blank">«Категорії проєктів»</a>.
            </div>
        </div>

        <div x-show="lang === 'uk'">
            <div class="mb-3">
                <label class="form-label">Назва (UA)</label>
                <input type="text" name="name[uk]" class="form-control"
                    value="{{ old('name.uk', $project->getTranslation('name', 'uk')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Опис (UA)</label>
                <textarea name="text[uk]" rows="8" class="form-control">{{ old('text.uk', $project->getTranslation('text', 'uk')) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Адреса (UA)</label>
                <input type="text" name="address[uk]" class="form-control"
                    value="{{ old('address.uk', $project->getTranslation('address', 'uk')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Інфраструктура (UA) — по одному пункту на рядок</label>
                <textarea name="infrastructure_uk" rows="6" class="form-control">{{ old('infrastructure_uk', implode("\n", $project->infrastructureFor('uk'))) }}</textarea>
            </div>
        </div>

        <div x-show="lang === 'en'">
            <div class="mb-3">
                <label class="form-label">Назва (EN)</label>
                <input type="text" name="name[en]" class="form-control"
                    value="{{ old('name.en', $project->getTranslation('name', 'en')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Опис (EN)</label>
                <textarea name="text[en]" rows="8" class="form-control">{{ old('text.en', $project->getTranslation('text', 'en')) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Адреса (EN)</label>
                <input type="text" name="address[en]" class="form-control"
                    value="{{ old('address.en', $project->getTranslation('address', 'en')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Інфраструктура (EN) — по одному пункту на рядок</label>
                <textarea name="infrastructure_en" rows="6" class="form-control">{{ old('infrastructure_en', implode("\n", $project->infrastructureFor('en'))) }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Зображення</div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Головне зображення</label>
                @if ($project->main_picture)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$project->main_picture) }}" alt="" style="max-height:120px;" class="rounded border">
                    </div>
                @endif
                <input type="file" name="main_picture" accept="image/*" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Логотип</label>
                @if ($project->logo)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$project->logo) }}" alt="" style="max-height:120px;" class="rounded border">
                    </div>
                @endif
                <input type="file" name="logo" accept="image/*" class="form-control">
            </div>
        </div>

        <hr>

        <label class="form-label">Галерея</label>
        @if ($project->relationLoaded('pictures') && $project->pictures->isNotEmpty())
            <div class="d-flex flex-wrap gap-3 mb-3">
                @foreach ($project->pictures as $picture)
                    <div class="text-center">
                        <img src="{{ asset('storage/'.$picture->path) }}" alt="" style="width:100px;height:100px;object-fit:cover;" class="rounded border d-block mb-1">
                        <label class="form-check-label small text-danger">
                            <input type="checkbox" name="delete_pictures[]" value="{{ $picture->id }}" class="form-check-input">
                            видалити
                        </label>
                    </div>
                @endforeach
            </div>
        @endif
        <input type="file" name="gallery[]" accept="image/*" multiple class="form-control">
        <div class="form-text">Можна вибрати кілька файлів одразу — додадуться до вже наявної галереї.</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Характеристики</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Телефон</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $project->phone) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Сайт проєкту</label>
                <input type="text" name="website" class="form-control" value="{{ old('website', $project->website) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Забудовник</label>
                <input type="text" name="developer" class="form-control" value="{{ old('developer', $project->developer) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Генпідрядник</label>
                <input type="text" name="general_contractor" class="form-control" value="{{ old('general_contractor', $project->general_contractor) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Проєктний офіс</label>
                <input type="text" name="project_office" class="form-control" value="{{ old('project_office', $project->project_office) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Загальна площа</label>
                <input type="text" name="full_area" class="form-control" value="{{ old('full_area', $project->full_area) }}">
            </div>
            <div class="col-md-12">
                <label class="form-label">Відео (посилання на YouTube або .mp4)</label>
                <input type="text" name="video" class="form-control" value="{{ old('video', $project->video) }}">
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Публікація</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="draft" @selected(old('status', $project->status) === 'draft')>Чернетка</option>
                    <option value="published" @selected(old('status', $project->status) === 'published')>Опубліковано</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Дата публікації</label>
                <input type="datetime-local" name="published_at" class="form-control"
                    value="{{ old('published_at', optional($project->published_at ?? now())->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Порядок сортування</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $project->sort_order) }}">
            </div>
        </div>
        <div class="form-check form-switch mt-3">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured"
                @checked(old('is_featured', $project->is_featured))>
            <label class="form-check-label" for="is_featured">Рекомендований (слайдер на головній та у списку проєктів)</label>
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
                    value="{{ old('meta_title.uk', $project->getTranslation('meta_title', 'uk')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta description (UA)</label>
                <textarea name="meta_description[uk]" maxlength="160" rows="2" class="form-control">{{ old('meta_description.uk', $project->getTranslation('meta_description', 'uk')) }}</textarea>
            </div>
        </div>
        <div x-show="lang === 'en'">
            <div class="mb-3">
                <label class="form-label">Meta title (EN)</label>
                <input type="text" name="meta_title[en]" maxlength="70" class="form-control"
                    value="{{ old('meta_title.en', $project->getTranslation('meta_title', 'en')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta description (EN)</label>
                <textarea name="meta_description[en]" maxlength="160" rows="2" class="form-control">{{ old('meta_description.en', $project->getTranslation('meta_description', 'en')) }}</textarea>
            </div>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-dark">Зберегти</button>
<a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">Скасувати</a>
