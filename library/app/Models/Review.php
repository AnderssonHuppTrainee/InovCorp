<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'book_request_id',
        'user_id',
        'rating',
        'comment',
        'reason',
        'status'
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    public function bookRequest()
    {
        return $this->belongsTo(BookRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
