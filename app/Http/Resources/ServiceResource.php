<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Service */
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => $this->link,
            'external' => str_starts_with($this->link, 'http'),
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'imageAlt' => $this->getTranslation('image_alt', app()->getLocale(), false) ?: null,
        ];
    }
}
