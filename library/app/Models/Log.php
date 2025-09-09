<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'date',
        'user_id',
        'module',
        'object_id',
        'changes',
        'ip',
        'browser'
    ];

    //relacao com o user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
