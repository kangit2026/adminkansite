<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SiteSetting extends Model
{
    use HasTranslations;

    public array $translatable = [
        'home_meta_title',
        'home_meta_description',
        'news_meta_title',
        'news_meta_description',
        'projects_meta_title',
        'projects_meta_description',
        'services_meta_title',
        'services_meta_description',
        'commercial_ad_title',
        'project_ad_title',
        'copyright_text',
        'motto',
        'awards_title',
        'award_first_description',
        'award_second_description',
        'award_third_description',
        'award_fourth_description',
    ];

    protected $fillable = [
        'home_meta_title',
        'home_meta_description',
        'home_og_image',
        'home_video',
        'news_meta_title',
        'news_meta_description',
        'projects_meta_title',
        'projects_meta_description',
        'services_meta_title',
        'services_meta_description',
        'commercial_ad_title',
        'commercial_ad_link',
        'project_ad_title',
        'project_ad_link',
        'phone',
        'email',
        'telegram_chat_link',
        'social_instagram',
        'social_youtube',
        'social_facebook',
        'social_pinterest',
        'social_telegram',
        'copyright_text',
        'copyright_year',
        'motto',
        'awards_title',
        'award_first_value',
        'award_first_description',
        'award_second_value',
        'award_second_description',
        'award_third_value',
        'award_third_description',
        'award_fourth_value',
        'award_fourth_description',
    ];

    protected function casts(): array
    {
        return [
            'copyright_year' => 'integer',
            'award_first_value' => 'integer',
            'award_second_value' => 'integer',
            'award_third_value' => 'integer',
            'award_fourth_value' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
