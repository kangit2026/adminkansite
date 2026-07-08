<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\AboutPage */
class AboutPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'motto' => $this->motto,
            'heading' => $this->heading,
            'history' => $this->history,
            'specialists' => $this->specialists,
            'today' => [
                'title' => $this->today_title,
                'description' => $this->today_description,
            ],
            'merit' => [
                'title' => $this->merit_title,
                'description' => $this->merit_description,
                'list' => array_values(array_filter([
                    $this->merit_list_1,
                    $this->merit_list_2,
                    $this->merit_list_3,
                    $this->merit_list_4,
                ], fn ($v) => $v !== null && $v !== '')),
            ],
            'achievementsText' => $this->achievements_text,
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
            'ogImage' => $this->og_image ? asset('storage/'.$this->og_image) : null,
        ];
    }
}
