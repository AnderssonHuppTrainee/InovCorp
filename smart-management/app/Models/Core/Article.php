<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Core\Proposal\ProposalItem;
use App\Models\Financial\TaxRate;
use App\Models\Core\Order\OrderItem;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Article extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'reference',
        'name',
        'description',
        'price',
        'tax_rate_id',
        'photo',
        'observations',
        'status'
    ];

    protected $casts = [
        'reference' => 'encrypted',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function proposalItems()
    {
        return $this->hasMany(ProposalItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeFilter($query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        })->when(isset($filters['status']) && $filters['status'] !== 'all', function ($query) use ($filters) {
            $query->where('status', $filters['status']);
        });
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference', 'name', 'price', 'status'])
            ->logOnlyDirty();
    }
}
