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
        'copyright_text',
    ];

    protected $fillable = [
        'home_meta_title',
        'home_meta_description',
        'home_og_image',
        'home_robots_index',
        'home_robots_follow',
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
    ];

    protected function casts(): array
    {
        return [
            'home_robots_index' => 'boolean',
            'home_robots_follow' => 'boolean',
            'copyright_year' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
