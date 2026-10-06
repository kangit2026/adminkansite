<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CareerPage extends Model
{
    use HasTranslations;

    protected $table = 'career_page';

    public array $translatable = [
        'heading',
        'city_label',
        'all_categories_label',
        'intro_text',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'heading',
        'city_label',
        'all_categories_label',
        'intro_text',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
