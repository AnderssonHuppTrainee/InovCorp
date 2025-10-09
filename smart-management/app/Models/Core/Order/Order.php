<?php

namespace App\Models\Core\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Entity;
use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Order\OrderItem;
use App\Models\Core\DigitalArchive;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'order_date',
        'client_id',
        'proposal_id',
        'delivery_date',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'number' => 'encrypted',
        'order_date' => 'date',
        'delivery_date' => 'date',
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

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
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
        })->when($filters['client_id'] ?? null, function ($query, $clientId) {
            $query->where('client_id', $clientId);
        });
    }

    // Gerar número sequencial
    public static function nextNumber(): string
    {
        $lastNumber = static::withTrashed()->max('number');
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    // Calcular total
    public function calculateTotal()
    {
        $total = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $this->update(['total_amount' => $total]);

        return $total;
    }

    // Converter para Encomendas de Fornecedores (agrupa por fornecedor)
    public function convertToSupplierOrders()
    {
        // Agrupar itens por fornecedor
        $itemsBySupplier = $this->items->groupBy('supplier_id');

        $supplierOrders = [];

        foreach ($itemsBySupplier as $supplierId => $items) {
            if (!$supplierId) {
                continue; // Pular itens sem fornecedor
            }

            // Calcular total deste fornecedor
            $totalAmount = $items->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });

            // Criar encomenda de fornecedor
            $supplierOrder = SupplierOrder::create([
                'number' => SupplierOrder::nextNumber(),
                'order_date' => now(),
                'supplier_id' => $supplierId,
                'order_id' => $this->id,
                'total_amount' => $totalAmount,
                'status' => 'draft',
            ]);

            $supplierOrders[] = $supplierOrder;
        }

        return $supplierOrders;
    }
}
