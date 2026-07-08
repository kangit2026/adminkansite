<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutPage extends Model
{
    use HasTranslations;

    protected $table = 'about_page';

    public array $translatable = [
        'motto',
        'heading',
        'history',
        'specialists',
        'today_title',
        'today_description',
        'merit_title',
        'merit_description',
        'merit_list_1',
        'merit_list_2',
        'merit_list_3',
        'merit_list_4',
        'achievements_text',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'motto',
        'heading',
        'history',
        'specialists',
        'today_title',
        'today_description',
        'merit_title',
        'merit_description',
        'merit_list_1',
        'merit_list_2',
        'merit_list_3',
        'merit_list_4',
        'achievements_text',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
