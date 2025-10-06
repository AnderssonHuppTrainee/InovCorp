<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
{
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
