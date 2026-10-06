<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CareerPage */
class CareerPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'heading' => $this->heading,
            'cityLabel' => $this->city_label,
            'allCategoriesLabel' => $this->all_categories_label,
            'introText' => $this->intro_text,
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
            'ogImage' => $this->og_image ? asset('storage/'.$this->og_image) : null,
        ];
    }
}
