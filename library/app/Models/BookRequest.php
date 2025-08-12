<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    protected $fillable = [
        'number',
        'user_id',
        'book_id',
        'request_date',
        'expected_return_date',
        'actual_receipt_date',
        'actual_days',
        'status',
        'photo_path'
    ];
    protected $casts = [
        'request_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_receipt_date' => 'datetime',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            // Gerar número sequencial
            $lastRequest = BookRequest::orderBy('id', 'desc')->first();
            $request->number = 'REQ-' . str_pad($lastRequest ? $lastRequest->id + 1 : 1, 6, '0', STR_PAD_LEFT);

            // Definir data de retorno esperada (5 dias após)
            $request->expected_return_date = now()->addDays(5);
        });
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function isOverdue()
    {
        return $this->status === 'approved' &&
            $this->expected_return_date < now() &&
            $this->returned_at === null;
    }

}
