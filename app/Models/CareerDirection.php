<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CareerDirection extends Model
{
    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class, 'direction_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
