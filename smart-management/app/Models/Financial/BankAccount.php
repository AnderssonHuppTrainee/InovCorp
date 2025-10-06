<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Routing\Loader\Configurator\Traits\AddTrait;

class BankAccount extends Model
{
    /** @use HasFactory<\Database\Factories\BankAccountFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'account_number',
        'iban',
        'swift',
        'bank_name',
        'balance',
        'currency',
        'is_active'
    ];

    protected $casts = [
        'name' => 'string',
        'encrypted',
        'account_number' => 'encrypted',
        'iban' => 'encrypted',
        'bank_name' => 'encrypted',
        'balance' => 'encrypted',
        'currency' => 'encrypted',
    ];

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }

}
