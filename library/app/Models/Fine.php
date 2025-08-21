<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'book_request_id',
        'amount',
        'reason',
        'status',
        'paid_at',
    ];

    public function bookRequest()
    {
        return $this->belongsTo(BookRequest::class);
    }
}