<?php

namespace App\Models\Core\Proposal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Core\Proposal\ProposalItem;
use App\Models\Core\Entity;
use App\Models\Core\Order\Order;
use App\Models\Core\DigitalArchive;
class Proposal extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalsFactory> */
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'number',
        'proposal_date',
        'client_id',
        'validity_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'validity_date' => 'date'
    ];
    public function client()
    {
        return $this->belongsTo(Entity::class, 'client_id');
    }
    public function items()
    {
        return $this->hasMany(ProposalItem::class);
    }
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function documents()
    {
        return $this->morphMany(DigitalArchive::class, 'archivable')
            ->where('document_type', 'proposal_pdf');
    }

    // scopes
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


    public static function nextNumber(): string
    {
        $lastNumber = static::withTrashed()->max('number');
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }


    public function calculateTotal()
    {
        $total = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $this->update(['total_amount' => $total]);

        return $total;
    }


    public function convertToOrder()
    {
        $order = Order::create([
            'number' => Order::nextNumber(),
            'order_date' => now(),
            'client_id' => $this->client_id,
            'proposal_id' => $this->id,
            'delivery_date' => now()->addDays(30),
            'total_amount' => $this->total_amount,
            'status' => 'draft', // rascunho
        ]);


        foreach ($this->items as $item) {
            $order->items()->create([
                'article_id' => $item->article_id,
                'supplier_id' => $item->supplier_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'notes' => $item->notes,
            ]);
        }
        //atualiza o estado da proposta
        $this->update(['status' => 'closed']);

        return $order;
    }

    public function generatePdf()
    {
        $pdfPath = "proposals/{$this->number}.pdf";

        // criar registro
        return DigitalArchive::create([
            'name' => "Proposta {$this->number}",
            'file_name' => "proposta-{$this->number}.pdf",
            'file_path' => $pdfPath,
            'document_type' => 'proposal_pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 0,
            'archivable_id' => $this->id,
            'archivable_type' => self::class,
            'uploaded_by' => auth()->id()
        ]);
    }
}
