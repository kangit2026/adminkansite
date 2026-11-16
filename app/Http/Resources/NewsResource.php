<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin \App\Models\News */
class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'text' => $this->text,
            // Окремих SEO-полів в адмінці немає: title = заголовок, description = початок тексту.
            'metaTitle' => $this->title,
            'metaDescription' => $this->excerpt($this->text),
            'date' => optional($this->published_at)->toIso8601String(),
            'picture' => $this->picture ? asset('storage/'.$this->picture) : null,
            'pictureAlt' => $this->getTranslation('picture_alt', app()->getLocale(), false) ?: null,
            'external_link' => $this->external_link,
            'big_picture' => $this->big_picture,
            'tags' => $this->whenLoaded(
                'tags',
                fn () => $this->tags->map(fn ($tag) => $tag->name)->values()
            ),
            // Заповнюються тільки в Api\NewsController@show (транзиентні
            // атрибути моделі, не з БД); у списку завжди null.
            'prev_slug' => $this->resource->getAttribute('prev_slug'),
            'next_slug' => $this->resource->getAttribute('next_slug'),
        ];
    }

    /** Перші ~160 символів тексту без HTML, по цілих словах. */
    private function excerpt(?string $html): ?string
    {
        // пробіл на межі абзаців, щоб «…кінець.</p><p>Початок…» не злиплися
        $html = preg_replace('#</?(p|br|li|h[1-6]|div|blockquote)\b[^>]*>#i', ' ', (string) $html);
        $plain = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = trim(preg_replace('/\s+/u', ' ', $plain));

        return $plain === '' ? null : Str::limit($plain, 160, '…', preserveWords: true);
    }
}
