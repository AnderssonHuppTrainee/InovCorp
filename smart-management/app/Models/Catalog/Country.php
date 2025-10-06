<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;

class Country extends Model
{
    protected $fillable = ['name', 'code', 'phone_code', 'is_active'];

    public function entities()
    {
        return $this->hasMany(Entity::class);
    }
}
