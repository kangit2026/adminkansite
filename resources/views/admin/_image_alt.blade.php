<label class="form-label mt-2">Підпис (UA)</label>
<input type="text" name="{{ $name }}[uk]" maxlength="255" class="form-control" value="{{ old($name.'.uk', $model->getTranslation($attribute, 'uk', false)) }}">
<label class="form-label mt-2">Alt (EN)</label>
<input type="text" name="{{ $name }}[en]" maxlength="255" class="form-control" value="{{ old($name.'.en', $model->getTranslation($attribute, 'en', false)) }}">
<div class="form-text">Якщо підпис порожній, на сайті буде назва.</div>
