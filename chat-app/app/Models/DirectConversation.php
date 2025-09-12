<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectConversation extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'direct_conversation_user');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
