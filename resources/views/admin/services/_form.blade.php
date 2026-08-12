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

        <div x-show="lang === 'uk'" class="mb-3">
            <label class="form-label">Назва (UA)</label>
            <input type="text" name="title[uk]" class="form-control"
                value="{{ old('title.uk', $service->getTranslation('title', 'uk')) }}">
        </div>
        <div x-show="lang === 'en'" class="mb-3">
            <label class="form-label">Назва (EN)</label>
            <input type="text" name="title[en]" class="form-control"
                value="{{ old('title.en', $service->getTranslation('title', 'en')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Посилання</label>
            <input type="text" name="link" class="form-control" value="{{ old('link', $service->link) }}" placeholder="/platform або https://...">
            <div class="form-text">Внутрішній шлях сайту (напр. <code>/platform</code>) або зовнішнє посилання — за цим автоматично обирається іконка (стрілка/глобус).</div>
        </div>

        <div class="mb-1">
            <label class="form-label">Зображення (прев'ю при наведенні)</label>
            @if ($service->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$service->image) }}" alt="" style="max-height:120px;" class="rounded border">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-semibold">Публікація</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="draft" @selected(old('status', $service->status) === 'draft')>Чернетка</option>
                    <option value="published" @selected(old('status', $service->status) === 'published')>Опубліковано</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Порядок сортування</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order) }}">
            </div>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-dark">Зберегти</button>
<a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Скасувати</a>
