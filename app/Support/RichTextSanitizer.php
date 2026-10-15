<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class RichTextSanitizer
{
    /** @param  array<string, string|null>|null  $values */
    public static function cleanTranslatable(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach ($values as $locale => $html) {
            $values[$locale] = self::clean($html);
        }

        return $values;
    }

    public static function clean(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $trimmed = trim($html);
        if ($trimmed === '') {
            return '';
        }

        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set(
            'HTML.Allowed',
            'p,br,strong,b,em,i,u,a[href|title|target|rel],ul,ol,li,h2,h3,blockquote'
        );
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
        ]);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.TargetBlank', true);

        return (new HTMLPurifier($config))->purify($html);
    }

    /**
     * Текст проєкту в базі — звичайні абзаци через порожній рядок.
     * Редактор показує їх як HTML, інакше склеює в один абзац.
     */
    public static function plainTextToEditorHtml(?string $text): string
    {
        if ($text === null || trim($text) === '') {
            return '';
        }

        if (preg_match('/<(p|br|div|ul|ol|li|h[1-6]|blockquote)\b/i', $text)) {
            return $text;
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $paragraphs = preg_split("/\n{2,}/", trim($text)) ?: [];
        $html = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                continue;
            }

            $lines = array_map(
                fn (string $line) => e(trim($line)),
                explode("\n", $paragraph)
            );
            $html[] = '<p>'.implode('<br>', $lines).'</p>';
        }

        return implode('', $html);
    }

    /** @param  array<string, string|null>|null  $values */
    public static function plainTranslatable(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach ($values as $locale => $html) {
            $values[$locale] = self::editorHtmlToPlainText($html);
        }

        return $values;
    }

    public static function editorHtmlToPlainText(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html) ?? $html;
        $html = preg_replace('/<\/p>/i', "\n\n", $html) ?? $html;
        $html = preg_replace('/<\/(div|h[1-6]|li|blockquote)>/i', "\n", $html) ?? $html;
        $html = strip_tags($html);
        $html = str_replace("\u{00A0}", ' ', $html);

        $lines = array_map(
            fn (string $line) => trim(preg_replace('/[ \t]+/u', ' ', $line) ?? $line),
            explode("\n", $html)
        );
        $text = preg_replace("/\n{3,}/", "\n\n", implode("\n", $lines)) ?? '';

        return trim($text);
    }
}
