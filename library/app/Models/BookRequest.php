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
        return $this->status === 'approved' &&
            $this->expected_return_date < now() &&
            $this->returned_at === null;
    }
    //função para aplicar coima caso livro danificado ou perdido
    public function calculateFine(string $condition): array
    {
        $book = $this->book;
        $daysUsed = $this->request_date->diffInDays(now());

        $daysLate = now()->greaterThan($this->expected_return_date)
            ? $this->expected_return_date->diffInDays(now())
            : 0;

        $fine = 0;
        $reason = null;

        // coima por atraso
        if ($daysLate > 0) {
            $fine += $daysLate * 0.10; // 10c por dia
            $reason = "Atraso de {$daysLate} dias";
        }

        //coima por dano
        if (in_array($condition, ['Bad', 'Dammage'])) {
            $fine += 10;
            $reason = $reason ? $reason . ' + Dano' : 'Dano';
        }
        //coima por livro perdido
        if ($condition == 'Lost') {
            $price = $book?->price ?? 0;
            $fine += $price;
            $reason = $reason ? $reason . ' + Livro Perdido' : 'Livro Perdido';
        }

        return [
            'days_used' => $daysUsed,
            'days_late' => $daysLate,
            'fine' => $fine,
            'reason' => $reason,
        ];
    }

}
