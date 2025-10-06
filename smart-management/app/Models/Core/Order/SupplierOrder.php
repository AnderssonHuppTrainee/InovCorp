<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;
use App\Models\Order;
use App\Models\SupplierInvoice;

class SupplierOrder extends Model
{
    protected $fillable = [
        'number',
        'order_date',
        'supplier_id',
        'order_id',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'number' => 'encrypted',
        'order_date' => 'date'
    ];

    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }
}
