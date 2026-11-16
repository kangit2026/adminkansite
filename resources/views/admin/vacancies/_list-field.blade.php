{{-- Список пунктів: кожен пункт — окремий рядок-поле, на сайті стає пунктом списку.
     Параметри: $name (поле), $id, $label, $hint, $required, $placeholder. Дані — масив $vacancy->{$name}. --}}
@php
    $fromOld = old($name);
    $items = is_array($fromOld) ? array_values($fromOld) : array_values($vacancy->{$name} ?? []);
    if ($items === []) {
        $items = [''];
    }
@endphp
<div class="mb-4" x-data="listField(@js($items))">
    <label class="form-label" for="{{ $id }}-0">{{ $label }}</label>
    <div class="d-flex flex-column gap-2">
        <template x-for="(item, i) in items" :key="item.key">
            <div class="d-flex gap-2 align-items-start">
                <span class="pt-2 text-muted" style="width:1rem">•</span>
                <textarea rows="1" class="form-control" style="resize:none;overflow:hidden" name="{{ $name }}[]" :id="'{{ $id }}-' + i"
                          x-model="item.text" placeholder="{{ $placeholder ?? 'Новий пункт' }}"
                          x-init="$nextTick(() => resize($el))"
                          x-on:input="resize($el)"
                          x-on:keydown.enter.prevent="add(i)"
                          x-on:keydown.backspace="if (item.text === '' && items.length > 1) { $event.preventDefault(); remove(i, true) }"
                          x-on:paste="paste($event, i)"></textarea>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" title="Вгору" x-on:click="move(i, -1)" :disabled="i === 0">↑</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" title="Вниз" x-on:click="move(i, 1)" :disabled="i === items.length - 1">↓</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" title="Видалити пункт" x-on:click="remove(i)">×</button>
                </div>
            </div>
        </template>
    </div>
    <button type="button" class="btn btn-outline-primary btn-sm mt-2" x-on:click="add(items.length - 1)">+ Додати пункт</button>
    <div class="form-text">{{ $hint }} Enter — наступний пункт.</div>
</div>
