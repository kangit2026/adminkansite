<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\InsideCommunityBlock */
class InsideCommunityBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'layout' => $this->layout,
            'number' => $this->number,
            'sortOrder' => $this->sort_order,
            'title' => $this->title,
            'description' => $this->description,
            'statValue' => $this->stat_value,
            'statLabel' => $this->stat_label,
            'highlightSmall' => $this->highlight_small,
            'highlightBig' => $this->highlight_big,
            'highlightMedium' => $this->highlight_medium,
            'ctaText' => $this->cta_text,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'imageAlt' => $this->getTranslation('image_alt', app()->getLocale(), false) ?: null,
        ];
    }
}
