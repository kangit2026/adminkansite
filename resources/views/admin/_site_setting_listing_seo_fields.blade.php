{{-- $prefix: news_meta | projects_meta | services_meta --}}
<div class="card-body" x-data="{ lang: 'uk' }">
    @include('admin._lang_tabs')

    <div x-show="lang === 'uk'">
        <div class="mb-3">
            <label class="form-label">Meta title (UA)</label>
            <input type="text" name="{{ $prefix }}_title[uk]" maxlength="70" class="form-control"
                value="{{ old("{$prefix}_title.uk", $settings->getTranslation("{$prefix}_title", 'uk')) }}">
            <div class="form-text">До 70 символів.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Meta description (UA)</label>
            <textarea name="{{ $prefix }}_description[uk]" maxlength="160" rows="3" class="form-control">{{ old("{$prefix}_description.uk", $settings->getTranslation("{$prefix}_description", 'uk')) }}</textarea>
            <div class="form-text">До 160 символів.</div>
        </div>
    </div>

    <div x-show="lang === 'en'">
        <div class="mb-3">
            <label class="form-label">Meta title (EN)</label>
            <input type="text" name="{{ $prefix }}_title[en]" maxlength="70" class="form-control"
                value="{{ old("{$prefix}_title.en", $settings->getTranslation("{$prefix}_title", 'en')) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Meta description (EN)</label>
            <textarea name="{{ $prefix }}_description[en]" maxlength="160" rows="3" class="form-control">{{ old("{$prefix}_description.en", $settings->getTranslation("{$prefix}_description", 'en')) }}</textarea>
        </div>
    </div>
</div>
