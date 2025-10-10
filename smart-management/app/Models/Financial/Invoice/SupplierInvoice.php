<?php

namespace App\Models\Financial\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Order\SupplierOrder;
use App\Models\Core\Entity;
use App\Models\Core\DigitalArchive;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierInvoice extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'number',
        'invoice_date',
        'due_date',
        'supplier_id',
        'supplier_order_id',
        'total_amount',
        'document_path',
        'payment_proof_path',
        'status'
    ];

    protected $casts = [
        'number' => 'encrypted',
        'invoice_date' => 'date',
        'due_date' => 'date'
    ];

    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    public function documents()
    {
        return $this->morphMany(DigitalArchive::class, 'archivable')
            ->where('document_type', 'invoice_document');
    }

    public function paymentProofs()
    {
        return $this->morphMany(DigitalArchive::class, 'archivable')
            ->where('document_type', 'payment_proof');
    }

    // Scopes
    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending_payment')
            ->where('due_date', '<', now());
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
        })->when($filters['supplier_id'] ?? null, function ($query, $supplierId) {
            $query->where('supplier_id', $supplierId);
        });
    }

    // Gerar número sequencial
    public static function nextNumber(): string
    {
        $lastNumber = static::withTrashed()->max('number');
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    // Check if invoice is overdue
    public function isOverdue(): bool
    {
        return $this->status === 'pending_payment' && $this->due_date < now();
    }

    // Send payment proof email to supplier
    public function sendPaymentProofEmail()
    {
        $supplier = $this->supplier;

        if (!$supplier || !$supplier->email) {
            return false;
        }

        \Mail::to($supplier->email)->send(
            new \App\Mail\PaymentProofMail($this)
        );

        return true;
    }
}
