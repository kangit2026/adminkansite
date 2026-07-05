<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\SiteSetting */
class SiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'telegram_chat_link' => $this->telegram_chat_link,
            'social' => [
                'instagram' => $this->social_instagram,
                'youtube' => $this->social_youtube,
                'facebook' => $this->social_facebook,
                'pinterest' => $this->social_pinterest,
                'telegram' => $this->social_telegram,
            ],
            'copyright_text' => $this->copyright_text,
            'copyright_year' => $this->copyright_year,
            'home' => [
                'meta_title' => $this->home_meta_title,
                'meta_description' => $this->home_meta_description,
                'og_image' => $this->home_og_image ? asset('storage/'.$this->home_og_image) : null,
            ],
        ];
    }
}
