<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BankAccount;

class FinancialTransaction extends Model
{
    /** @use HasFactory<\Dataabase\Factories\FinancialTransactionFactory> */
    use HasFactory;


    protected $fillable = [
        'bank_account_id',
        'type',
        'amount',
        'description',
        'reference',
        'transaction_date',
        'metadata'
    ];

    protected $casts = [
        'reference' => 'encrypted',
        'transaction_date' => 'date',
        'metadata' => 'array'
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
