<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Validation\ValidationException;

/**
 * Квіз заявки на головній («Що вас цікавить?»): перше питання + гілки питань за його відповіддю.
 *
 * Форма JSON (кожен текст — ['uk' => …, 'en' => …]):
 *   project_step — id кроку, відповідь на який є проєктом заявки (поле «Проєкт»)
 *   intent       — перше питання: id, label, question, hint, options[]
 *   paths        — [значення відповіді на перше питання => [крок, …]]
 *   крок         — id, label, question, hint, options[]
 *   варіант      — value (стабільний ключ), label, enabled
 */
class HomeQuiz
{
    private const LANGS = ['uk', 'en'];

    /** Типовий квіз (той самий, що в коді сайту): поки в адмінці нічого не збережено. */
    public static function defaults(): array
    {
        return json_decode(file_get_contents(database_path('data/home-quiz.json')), true);
    }

    public static function current(): array
    {
        $saved = SiteSetting::current()->home_quiz;

        return is_array($saved) && isset($saved['intent'], $saved['paths']) ? $saved : self::defaults();
    }

    /** Те, що бачить сайт: без вимкнених варіантів і кроків, де не лишилось відповідей. */
    public static function published(): array
    {
        $quiz = self::current();
        $live = fn (array $step) => ['options' => array_values(array_filter($step['options'], fn ($o) => $o['enabled'] ?? true))] + $step;

        $intent = $live($quiz['intent']);
        $paths = [];
        foreach ($intent['options'] as $option) {
            $steps = array_map($live, $quiz['paths'][$option['value']] ?? []);
            $paths[$option['value']] = array_values(array_filter($steps, fn ($s) => $s['options']));
        }

        return ['project_step' => $quiz['project_step'] ?? null, 'intent' => $intent, 'paths' => $paths];
    }

    /**
     * Відповіді з форми ({id кроку: значення}) → поля заявки «Питання: відповідь» українською, у порядку питань.
     * Невідомі кроки й значення відкидаються або лишаються як є (якщо квіз змінили, поки людина відповідала).
     */
    public static function payloadFor(array $answers): array
    {
        $quiz = self::current();
        $intentValue = (string) ($answers[$quiz['intent']['id']] ?? '');
        $steps = array_merge([$quiz['intent']], $quiz['paths'][$intentValue] ?? []);

        $payload = [];
        foreach ($steps as $step) {
            $value = $answers[$step['id']] ?? null;
            if (! is_string($value) || $value === '') {
                continue;
            }
            $label = collect($step['options'])->firstWhere('value', $value)['label']['uk'] ?? null;
            $payload[$step['label']['uk'] ?: $step['id']] = mb_substr($label ?: $value, 0, 255);
        }

        return $payload;
    }

    /** Перевіряє й чистить квіз із форми адмінки; при помилці — ValidationException з ключем `quiz`. */
    public static function sanitize(mixed $input): array
    {
        if (! is_array($input) || ! isset($input['intent'], $input['paths']) || ! is_array($input['paths'])) {
            self::fail('Квіз порожній або пошкоджений. Оновіть сторінку й спробуйте ще раз.');
        }

        $intent = self::step($input['intent'], 'Перше питання');
        $enabled = array_values(array_filter($intent['options'], fn ($o) => $o['enabled']));
        if (! $enabled) {
            self::fail('Першe питання: увімкніть хоча б один варіант відповіді.');
        }

        $paths = [];
        $ids = [$intent['id'] => true];
        foreach ($intent['options'] as $option) {
            $steps = [];
            foreach ((array) ($input['paths'][$option['value']] ?? []) as $i => $raw) {
                $name = 'Гілка «'.($option['label']['uk'] ?: $option['value']).'», питання '.($i + 1);
                $step = self::step($raw, $name);
                if (isset($ids[$step['id']])) {
                    self::fail("$name: повторюється ідентифікатор кроку. Видаліть питання й додайте його знову.");
                }
                $ids[$step['id']] = true;
                $steps[] = $step;
            }
            $paths[$option['value']] = $steps;
        }

        $project = is_string($input['project_step'] ?? null) && isset($ids[$input['project_step']]) ? $input['project_step'] : null;

        return ['project_step' => $project, 'intent' => $intent, 'paths' => $paths];
    }

    private static function step(mixed $raw, string $name): array
    {
        if (! is_array($raw)) {
            self::fail("$name: пошкоджені дані.");
        }

        $id = self::key($raw['id'] ?? '', "$name: ідентифікатор кроку");
        $options = [];
        $values = [];
        foreach ((array) ($raw['options'] ?? []) as $i => $o) {
            $value = self::key($o['value'] ?? '', "$name: ключ варіанта ".($i + 1));
            if (isset($values[$value])) {
                self::fail("$name: повторюється ключ варіанта «{$value}».");
            }
            $values[$value] = true;
            $label = self::text($o['label'] ?? [], 120);
            if ($label['uk'] === '' || $label['en'] === '') {
                self::fail("$name: у варіанта ".($i + 1).' заповніть текст українською й англійською.');
            }
            $options[] = ['value' => $value, 'label' => $label, 'enabled' => (bool) ($o['enabled'] ?? true)];
        }

        $step = [
            'id' => $id,
            'label' => self::text($raw['label'] ?? [], 40),
            'question' => self::text($raw['question'] ?? [], 160),
            'hint' => self::text($raw['hint'] ?? [], 240),
            'options' => $options,
        ];

        foreach (['label' => 'коротка назва', 'question' => 'питання'] as $field => $title) {
            if ($step[$field]['uk'] === '' || $step[$field]['en'] === '') {
                self::fail("$name: заповніть $title українською й англійською.");
            }
        }
        if (! array_filter($options, fn ($o) => $o['enabled'])) {
            self::fail("$name: увімкніть хоча б один варіант відповіді.");
        }

        return $step;
    }

    private static function text(mixed $raw, int $max): array
    {
        $out = [];
        foreach (self::LANGS as $lang) {
            $out[$lang] = mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($raw[$lang] ?? '')))), 0, $max);
        }

        return $out;
    }

    private static function key(mixed $value, string $name): string
    {
        $value = trim((string) $value);
        if (! preg_match('/^[a-z0-9][a-z0-9_-]{0,39}$/', $value)) {
            self::fail("$name некоректний.");
        }

        return $value;
    }

    private static function fail(string $message): never
    {
        throw ValidationException::withMessages(['quiz' => $message]);
    }
}
