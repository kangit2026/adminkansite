<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class InsideCommunityPage extends Model
{
    use HasTranslations;

    protected $table = 'inside_community_page';

    public array $translatable = [
        'hero_title',
        'hero_subtitle',
        'hero_paragraph_1',
        'hero_paragraph_2',
        'hero_paragraph_3',
        'philosophy_title',
        'philosophy_subtitle',
        'meta_title',
        'meta_description',
        'hero_image_alt',
        'complex_logo_1_alt',
        'complex_logo_2_alt',
    ];

    protected $fillable = [
        'hero_video',
        'hero_image_uk',
        'hero_image_en',
        'hero_image_alt',
        'complex_logo_1',
        'complex_logo_1_alt',
        'complex_logo_2',
        'complex_logo_2_alt',
        'hero_title',
        'hero_subtitle',
        'hero_paragraph_1',
        'hero_paragraph_2',
        'hero_paragraph_3',
        'philosophy_title',
        'philosophy_subtitle',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
