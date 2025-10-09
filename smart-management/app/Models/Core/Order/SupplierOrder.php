<?php

namespace App\Models\Core\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Entity;
use App\Models\Core\Order\Order;
use App\Models\Financial\Invoice\SupplierInvoice;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOrder extends Model
{
    use SoftDeletes;
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

    // Gerar número sequencial
    public static function nextNumber(): string
    {
        $lastNumber = static::withTrashed()->max('number');
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
