<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectConversation extends Model
{
    protected $fillable = [
        'created_by'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'direct_conversation_user');
    }

    public function messages()
    {
        return $this->hasMany(DirectMessage::class, 'direct_conversation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
