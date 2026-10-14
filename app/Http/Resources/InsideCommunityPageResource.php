<?php

namespace App\Http\Resources;

use App\Models\InsideCommunityBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\InsideCommunityPage */
class InsideCommunityPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'hero' => [
                'video' => $this->hero_video ? asset('storage/'.$this->hero_video) : '/inside_community/kan_inside.mp4',
                'imageUk' => $this->hero_image_uk ? asset('storage/'.$this->hero_image_uk) : null,
                'imageEn' => $this->hero_image_en ? asset('storage/'.$this->hero_image_en) : null,
                'imageAlt' => $this->getTranslation('hero_image_alt', app()->getLocale(), false) ?: null,
                'title' => $this->hero_title,
                'subtitle' => $this->hero_subtitle,
                'paragraphs' => array_values(array_filter([
                    $this->hero_paragraph_1,
                    $this->hero_paragraph_2,
                    $this->hero_paragraph_3,
                ], fn ($v) => $v !== null && $v !== '')),
            ],
            'complexLogos' => array_values(array_filter([
                $this->complex_logo_1 ? asset('storage/'.$this->complex_logo_1) : '/inside_community/respublica.svg',
                $this->complex_logo_2 ? asset('storage/'.$this->complex_logo_2) : '/inside_community/faina.svg',
            ])),
            'complexLogoAlts' => [
                $this->getTranslation('complex_logo_1_alt', app()->getLocale(), false) ?: null,
                $this->getTranslation('complex_logo_2_alt', app()->getLocale(), false) ?: null,
            ],
            'philosophy' => [
                'title' => $this->philosophy_title,
                'subtitle' => $this->philosophy_subtitle,
            ],
            'blocks' => InsideCommunityBlockResource::collection(
                InsideCommunityBlock::query()
                    ->published()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
            ),
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
            'ogImage' => $this->og_image ? asset('storage/'.$this->og_image) : null,
        ];
    }
}
