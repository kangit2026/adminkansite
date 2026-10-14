<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
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
}
