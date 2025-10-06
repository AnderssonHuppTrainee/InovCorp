<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;
use App\Models\Proposal;
use App\Models\OrderItem;
use App\Models\DigitalArchive;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'proposal_date',
        'client_id',
        'proposal_id',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'number',
        'proposal_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Entity::class, 'client_id');
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function supplierOrders()
    {
        return $this->hasMany(SupplierOrder::class);
    }
    public function documents()
    {
        return $this->morphMany(DigitalArchive::class, 'archivable')
            ->where('document_type', 'order_pdf');
    }

}
