<?php

namespace App\Models\Financial\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Order\Order;
use App\Models\Core\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'invoice_date',
        'due_date',
        'customer_id',
        'order_id',
        'total_amount',
        'paid_amount',
        'balance',
        'notes',
        'status'
    ];

    protected $casts = [
        'number' => 'encrypted',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];


    public function customer()
    {
        return $this->belongsTo(Entity::class, 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partially_paid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->whereIn('status', ['sent', 'partially_paid'])
                    ->where('due_date', '<', now());
            });
    }

    public function scopeFilter($query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('number', 'like', "%{$search}%");
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        })->when($filters['customer_id'] ?? null, function ($query, $customerId) {
            $query->where('customer_id', $customerId);
        });
    }


    public static function nextNumber(): string
    {
        $lastNumber = static::withTrashed()->max('number');
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function registerPayment(float $amount): void
    {
        $this->paid_amount += $amount;
        $this->balance = $this->total_amount - $this->paid_amount;

        if ($this->balance <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partially_paid';
        }

        $this->save();
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, ['sent', 'partially_paid', 'overdue'])
            && $this->due_date < now();
    }

    public function updateStatus(): void
    {
        if ($this->balance <= 0 && $this->paid_amount >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($this->isOverdue()) {
            $this->status = 'overdue';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partially_paid';
        }

        $this->save();
    }
}



