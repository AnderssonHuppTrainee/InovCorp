<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Friendship extends Model
{
    /** @use HasFactory<\Database\Factories\FriendshipFactory> */
    use HasFactory;


    protected $fillable = [
        'user_id',
        'friend_id',
        'status'
    ];

    //user q envia solicitacao
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    //user q recebe a solicitacao
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
