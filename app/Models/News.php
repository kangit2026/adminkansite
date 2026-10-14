<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class News extends Model
{
    use HasTranslations;

    // slug — єдиний на всі мови (не перекладається)
    public array $translatable = [
        'title',
        'text',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'slug',
        'title',
        'text',
        'meta_title',
        'meta_description',
        'picture',
        'partner_logo',
        'external_link',
        'big_picture',
        'is_partner',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'big_picture' => 'boolean',
            'is_partner' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function tags()
    {
        return $this->belongsToMany(NewsTag::class, 'news_tag_pivot');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
