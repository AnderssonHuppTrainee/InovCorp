<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Financial\FinancialTransaction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Type\Decimal;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

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
        'account_number' => 'encrypted',
        'iban' => 'encrypted',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];


    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeFilter($query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%");
            });
        })->when(isset($filters['is_active']) && $filters['is_active'] !== 'all', function ($query) use ($filters) {
            $query->where('is_active', $filters['is_active'] === '1');
        });
    }


    public function updateBalance(float $amount, string $type = 'credit'): void
    {
        if ($type === 'credit') {
            $this->balance += $amount;
        } else {
            $this->balance -= $amount;
        }
        $this->save();
    }

    public function getFormattedBalance(): string
    {
        return number_format($this->balance, 2, ',', '.') . ' ' . $this->currency;
    }
}
