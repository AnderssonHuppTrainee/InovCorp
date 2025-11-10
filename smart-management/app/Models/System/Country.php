<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Entity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory;
    protected $fillable = ['name', 'code', 'phone_code', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function entities()
    {
        return $this->hasMany(Entity::class);
    }
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query)
    {
        return $query->where('is_active', false);
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
