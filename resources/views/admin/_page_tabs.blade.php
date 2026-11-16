{{--
  Вкладки однієї форми (Головна, Контакти, Футер): одна кнопка «Зберегти» на всі вкладки.
  Сторінка обгортає вкладки й форму в
    <div x-data="pageTabs(@js(array_keys($tabs)), @js($errorTabs->first()))">
  і передає сюди $tabs (['ключ' => 'Назва']) та $errorTabs (ключі вкладок з помилками).
  Відкрита вкладка — у #адресі: переживає перезавантаження, а з :action="'…#' + tab" і збереження
  (браузер зберігає # при редиректі). Після помилки відкривається перша вкладка з помилкою.
--}}
<ul class="nav nav-pills mb-4">
    @foreach ($tabs as $key => $label)
        <li class="nav-item">
            <button type="button" class="nav-link" :class="tab === '{{ $key }}' && 'active'" @click="open('{{ $key }}')">
                {{ $label }}
                @if ($errorTabs->contains($key))
                    <i class="bi bi-exclamation-circle-fill text-danger ms-1" title="Є помилка"></i>
                @endif
            </button>
        </li>
    @endforeach
</ul>

@once
    @push('admin-scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pageTabs', (keys, errorTab) => ({
                    tab: errorTab || (keys.includes(location.hash.slice(1)) ? location.hash.slice(1) : keys[0]),
                    init() { this.open(this.tab); },
                    open(name) { this.tab = name; history.replaceState(null, '', '#' + name); },
                }));
            });
        </script>
    @endpush
@endonce
