<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsTag extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = ['name'];

    public function news()
    {
        return $this->belongsToMany(News::class, 'news_tag_pivot');
    }
}
