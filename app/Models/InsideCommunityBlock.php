<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class InsideCommunityBlock extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'description',
        'stat_value',
        'stat_label',
        'highlight_small',
        'highlight_big',
        'highlight_medium',
        'cta_text',
        'image_alt',
    ];

    protected $fillable = [
        'type',
        'layout',
        'number',
        'sort_order',
        'status',
        'title',
        'description',
        'stat_value',
        'stat_label',
        'highlight_small',
        'highlight_big',
        'highlight_medium',
        'cta_text',
        'image',
        'image_alt',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
