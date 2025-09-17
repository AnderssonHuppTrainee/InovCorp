<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectMessage extends Model
{
    /** @use HasFactory<\Database\Factories\DirectMessageFactory> */
    use HasFactory;


    protected $fillable = ['conversation_id', 'sender_id', 'body'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function conversation()
    {
        return $this->belongsTo(DirectConversation::class, 'conversation_id');
    }
}
