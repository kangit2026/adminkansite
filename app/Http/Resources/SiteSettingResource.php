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
                'video' => $this->home_video ? asset('storage/'.$this->home_video) : null,
                'motto' => $this->motto,
                'awards_title' => $this->awards_title,
                'awards' => [
                    ['value' => $this->award_first_value, 'description' => $this->award_first_description],
                    ['value' => $this->award_second_value, 'description' => $this->award_second_description],
                    ['value' => $this->award_third_value, 'description' => $this->award_third_description],
                    ['value' => $this->award_fourth_value, 'description' => $this->award_fourth_description],
                ],
            ],
            'news' => [
                'meta_title' => $this->news_meta_title,
                'meta_description' => $this->news_meta_description,
            ],
            'projects' => [
                'meta_title' => $this->projects_meta_title,
                'meta_description' => $this->projects_meta_description,
            ],
            'services' => [
                'meta_title' => $this->services_meta_title,
                'meta_description' => $this->services_meta_description,
            ],
            'commercial_ad' => [
                'title' => $this->commercial_ad_title,
                'link' => $this->commercial_ad_link,
            ],
            'project_ad' => [
                'title' => $this->project_ad_title,
                'link' => $this->project_ad_link,
            ],
        ];
    }
}
