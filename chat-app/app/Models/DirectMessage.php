<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectMessage extends Model
{
    /** @use HasFactory<\Database\Factories\DirectMessageFactory> */
    use HasFactory;


    protected $fillable = ['direct_conversation_id', 'sender_id', 'body'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function directConversation()
    {
        return $this->belongsTo(DirectConversation::class, 'direct_conversation_id');
    }
}
