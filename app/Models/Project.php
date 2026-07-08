<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasTranslations;

    // slug — єдиний на всі мови (не перекладається), як і в News.
    public array $translatable = [
        'name',
        'text',
        'address',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'text',
        'address',
        'infrastructure_items',
        'meta_title',
        'meta_description',
        'main_picture',
        'logo',
        'video',
        'phone',
        'website',
        'full_area',
        'project_office',
        'general_contractor',
        'developer',
        'is_featured',
        'status',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'infrastructure_items' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function pictures()
    {
        return $this->hasMany(ProjectPicture::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function infrastructureFor(string $locale): array
    {
        return $this->infrastructure_items[$locale] ?? [];
    }
}
