<?php

namespace App\Models\Core;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Core\Contact;
use App\Models\Catalog\Country;
use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Order\Order;
use App\Models\Core\Order\SupplierOrder;
use App\Models\Financial\Invoice\SupplierInvoice;
use App\Models\Core\WorkOrder;
use App\Models\System\Calendar\CalendarEvent;
use Illuminate\Database\Eloquent\SoftDeletes;


class Entity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'types',
        'number',
        'tax_number',
        'name',
        'address',
        'postal_code',
        'city',
        'country_id',
        'phone',
        'mobile',
        'website',
        'email',
        'gdpr_consent',
        'observations',
        'status',

    ];

    protected $casts = [
        'types' => 'array',
        'gdpr_consent' => 'boolean',
        'tax_number' => 'encrypted',
        'phone' => 'encrypted',
        'mobile' => 'encrypted',
        'email' => 'encrypted',
        'address' => 'encrypted',
        'observations' => 'encrypted',
    ];


    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function clientProposals()
    {
        return $this->hasMany(Proposal::class, 'client_id');
    }

    public function clientOrders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function supplierOrders()
    {
        return $this->hasMany(SupplierOrder::class, 'supplier_id');
    }

    public function supplierInvoices()
    {
        return $this->hasMany(SupplierInvoice::class, 'supplier_id');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'client_id');
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeClients(Builder $query)
    {
        return $query->whereJsonContains('types', 'client');
    }

    public function scopeSuppliers(Builder $query)
    {
        return $query->whereJsonContains('types', 'supplier');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFilter(Builder $query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('tax_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        })->when($filters['status'] ?? null, function (Builder $query, $status) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        })->when($filters['country_id'] ?? null, function (Builder $query, $countryId) {
            $query->where('country_id', $countryId);
        });
    }
    //gerar num sequencial
    public static function nextNumber(): string
    {
        $lastNumber = static::withTrashed()->max('number');
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
