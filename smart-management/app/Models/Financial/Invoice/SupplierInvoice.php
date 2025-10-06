<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SupplierOrder;
use App\Models\Entity;
use App\Models\DigitalArchive;

class SupplierInvoice extends Model
{
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

}
