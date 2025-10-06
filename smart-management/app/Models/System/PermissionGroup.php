<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PermissionGroup extends Model
{
    protected $fillable = ['name', 'permissions', 'status'];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission_groups');
    }
}
