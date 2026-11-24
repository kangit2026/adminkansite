{{--
  Одне питання квізу. Живе всередині Alpine-компонента quizEditor (edit.blade.php): очікує змінну `step`.
  $intent = true — перше питання (його варіанти — це гілки; питання не можна видалити чи пересунути);
  інакше — питання гілки `key`, індекс `si` у x-for.
--}}
<div class="border rounded p-3 mb-3 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong class="small text-uppercase text-muted">
            @if ($intent) Перше питання @else Питання <span x-text="si + 1"></span> @endif
        </strong>
        @unless ($intent)
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" :disabled="si === 0" @click="move(q.paths[key], si, -1)" title="Вище"><i class="bi bi-arrow-up"></i></button>
                <button type="button" class="btn btn-outline-secondary" :disabled="si === q.paths[key].length - 1" @click="move(q.paths[key], si, 1)" title="Нижче"><i class="bi bi-arrow-down"></i></button>
                <button type="button" class="btn btn-outline-danger" @click="removeStep(key, si)" title="Видалити питання"><i class="bi bi-trash"></i></button>
            </div>
        @endunless
    </div>

    <div class="row g-2 mb-2">
        <div class="col-md-6">
            <label class="form-label small mb-1">Питання (укр)</label>
            <input type="text" class="form-control form-control-sm" maxlength="160" x-model="step.question.uk">
        </div>
        <div class="col-md-6">
            <label class="form-label small mb-1">Question (en)</label>
            <input type="text" class="form-control form-control-sm" maxlength="160" x-model="step.question.en">
        </div>
        <div class="col-md-6">
            <label class="form-label small mb-1">Підказка під питанням (укр, необов’язково)</label>
            <input type="text" class="form-control form-control-sm" maxlength="240" x-model="step.hint.uk">
        </div>
        <div class="col-md-6">
            <label class="form-label small mb-1">Hint (en, optional)</label>
            <input type="text" class="form-control form-control-sm" maxlength="240" x-model="step.hint.en">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Назва кроку (укр)</label>
            <input type="text" class="form-control form-control-sm" maxlength="40" x-model="step.label.uk">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Step name (en)</label>
            <input type="text" class="form-control form-control-sm" maxlength="40" x-model="step.label.en">
        </div>
        <div class="col-md-6 d-flex align-items-end">
            @unless ($intent)
                <div class="form-check small">
                    <input type="checkbox" class="form-check-input" :id="'proj-' + step.id" :checked="q.project_step === step.id" @change="q.project_step = $event.target.checked ? step.id : null">
                    <label class="form-check-label" :for="'proj-' + step.id">Відповідь — це квартал/проєкт (піде в поле «Проєкт» заявки)</label>
                </div>
            @endunless
        </div>
    </div>
    <div class="form-text mb-2">Назва кроку — це підпис у верхній стрічці квізу й назва поля в заявці (наприклад «Квартал»).</div>

    <div class="small text-muted mb-1">Варіанти відповідей</div>
    <template x-for="(o, oi) in step.options" :key="o.value">
        <div class="row g-2 align-items-center mb-2">
            <div class="col-auto">
                <div class="form-check form-switch mb-0" title="Показувати на сайті">
                    <input type="checkbox" class="form-check-input" x-model="o.enabled">
                </div>
            </div>
            <div class="col">
                <input type="text" class="form-control form-control-sm" maxlength="120" placeholder="Варіант (укр)" x-model="o.label.uk" :class="!o.enabled && 'text-muted'">
            </div>
            <div class="col">
                <input type="text" class="form-control form-control-sm" maxlength="120" placeholder="Option (en)" x-model="o.label.en" :class="!o.enabled && 'text-muted'">
            </div>
            <div class="col-auto small text-muted d-none d-lg-block" style="width:120px;" x-text="o.value" title="Ключ варіанта (не змінюється)"></div>
            <div class="col-auto">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary" :disabled="oi === 0" @click="move(step.options, oi, -1)"><i class="bi bi-arrow-up"></i></button>
                    <button type="button" class="btn btn-outline-secondary" :disabled="oi === step.options.length - 1" @click="move(step.options, oi, 1)"><i class="bi bi-arrow-down"></i></button>
                    <button type="button" class="btn btn-outline-danger" @click="removeOption(step, oi, {{ $intent ? 'true' : 'false' }})"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        </div>
    </template>
    <button type="button" class="btn btn-sm btn-outline-secondary" @click="addOption(step, {{ $intent ? 'true' : 'false' }})"><i class="bi bi-plus-lg"></i> Додати варіант</button>
</div>
