<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'private',
        'created_by'
    ];

    public function users()
    {
        //pode ter mts users
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id');
    }

    // msgs enviadas na sala
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // user que criou a sala
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
