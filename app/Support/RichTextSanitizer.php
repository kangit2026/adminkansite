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
}
