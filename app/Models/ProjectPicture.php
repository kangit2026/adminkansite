<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProjectPicture extends Model
{
    use HasTranslations;

    public array $translatable = ['alt'];

    protected $fillable = ['project_id', 'path', 'alt', 'sort_order'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
