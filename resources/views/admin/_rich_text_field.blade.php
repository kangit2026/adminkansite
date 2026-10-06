{{--
  $name — поле форми, напр. text[uk]
  $id — унікальний id textarea
  $value — поточний HTML
  $oldKey — ключ для old(), напр. text.uk
--}}
@php
    $oldKey = $oldKey ?? str_replace(['[', ']'], ['.', ''], $name);
    $height = $height ?? 360;
@endphp

<textarea
    name="{{ $name }}"
    id="{{ $id }}"
    class="form-control js-rich-text"
    rows="12"
    data-rich-text-height="{{ $height }}"
>{{ old($oldKey, $value) }}</textarea>

@once
    @push('admin-scripts')
        <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            (function () {
                const baseConfig = {
                    menubar: false,
                    plugins: 'lists link autolink code',
                    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link | removeformat | code',
                    height: 360,
                    branding: false,
                    promotion: false,
                    convert_urls: false,
                    entity_encoding: 'raw',
                    content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; line-height: 1.5; }',
                    setup(editor) {
                        editor.on('change input undo redo', () => editor.save());
                    },
                };

                function initRichTextFields() {
                    if (typeof tinymce === 'undefined') return;

                    document.querySelectorAll('textarea.js-rich-text').forEach((el) => {
                        if (el.dataset.tinymceReady === '1') return;
                        el.dataset.tinymceReady = '1';

                        const height = parseInt(el.dataset.richTextHeight || '360', 10);

                        tinymce.init({
                            ...baseConfig,
                            target: el,
                            height,
                        });
                    });
                }

                document.addEventListener('DOMContentLoaded', () => {
                    initRichTextFields();

                    document.querySelectorAll('form').forEach((form) => {
                        form.addEventListener('submit', () => {
                            if (typeof tinymce !== 'undefined') {
                                tinymce.triggerSave();
                            }
                        });
                    });
                });
            })();
        </script>
    @endpush
@endonce
