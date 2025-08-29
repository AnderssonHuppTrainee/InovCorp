<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;
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
