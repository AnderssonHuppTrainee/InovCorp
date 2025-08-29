<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'user_id',
        'book_id',
        'request_date',
        'expected_return_date',
        'actual_receipt_date',
        'actual_days',
        'status',
        'photo_path',
        'returned_date',
        'admin_confirmed_return_date',
        'book_condition',
        'return_photo_path',
    ];
    protected $casts = [
        'request_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_receipt_date' => 'datetime',
        'returned_date' => 'datetime',
        'admin_confirmed_return_date' => 'datetime',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            // gerar num sequencial
            $lastRequest = BookRequest::orderBy('id', 'desc')->first();
            $request->number = 'REQ-' . str_pad($lastRequest ? $lastRequest->id + 1 : 1, 6, '0', STR_PAD_LEFT);

            //define o prazo de devolução 5 dias
            //$request->expected_return_date = now()->addDays(5);
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
        return $this->status != 'cancelled' && $this->expected_return_date < now() &&
            $this->returned_at === null;
    }
    public function fines()
    {
        return $this->hasMany(Fine::class, 'book_request_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'book_request_id');
    }


    //função para aplicar coima caso livro, atrasado,danificado ou perdido
    public function calculateFine(string $condition): array
    {
        $book = $this->book;
        $daysUsed = $this->request_date->diffInDays(now());
        $daysLate = 0;

        if (now()->gt($this->expected_return_date)) {

            $daysLate = $this->expected_return_date->copy()->addDay()->startOfDay()->diffInDays(now()->startOfDay());
        }

        $fine = 0;
        $reasonParts = [];

        // coima por atraso
        if ($daysLate > 0) {
            $fine += $daysLate * 1.0; // 1 euro diario
            $reasonParts[] = "Atraso de {$daysLate} dia(s)";
        }

        // coima por dano
        if (in_array($condition, ['Bad', 'Damaged'])) {
            $fine += 5;
            $reasonParts[] = "Dano";
        }

        // coima livro perdido
        if ($condition === 'Lost') {
            $price = $book?->price ?? 0;
            $fine += $price;
            $reasonParts[] = "Livro Perdido";
        }

        $reason = $reasonParts ? implode(' + ', $reasonParts) : null;

        return [
            'days_used' => $daysUsed,
            'days_late' => $daysLate,
            'fine' => $fine,
            'reason' => $reason,
        ];
    }

}
