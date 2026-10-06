<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MarketPage */
class MarketPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'heroImage' => $this->hero_image ? asset('storage/'.$this->hero_image) : null,
            'phone' => $this->phone,
            'badge' => $this->badge,
            'title' => $this->title,
            'sellText' => $this->sell_text,
            'question' => $this->question,
            'answer' => $this->answer,
            'contactLabel' => $this->contact_label,
            'prosTitle' => $this->pros_title,
            'prosItems' => $this->prosItemsForLocale(),
            'trustText' => $this->trust_text,
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
            'ogImage' => $this->og_image ? asset('storage/'.$this->og_image) : null,
        ];
    }
}
