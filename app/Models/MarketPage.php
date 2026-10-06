<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MarketPage extends Model
{
    use HasTranslations;

    protected $table = 'market_page';

    public array $translatable = [
        'badge',
        'title',
        'sell_text',
        'question',
        'answer',
        'contact_label',
        'pros_title',
        'trust_text',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'hero_image',
        'phone',
        'badge',
        'title',
        'sell_text',
        'question',
        'answer',
        'contact_label',
        'pros_title',
        'pros_items',
        'trust_text',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    protected $casts = [
        'pros_items' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    /** @return string[] */
    public function prosItemsForLocale(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $items = $this->pros_items ?? [];

        if (! is_array($items)) {
            return [];
        }

        $list = $items[$locale] ?? $items['uk'] ?? [];

        return is_array($list) ? array_values(array_filter($list, fn ($v) => $v !== null && $v !== '')) : [];
    }
}
