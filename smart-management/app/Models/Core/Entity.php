<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Proposal;
use App\Models\Order;
use App\Models\SupplierOrder;
use App\Models\SupplierInvoice;
use App\Models\WorkOrder;
use App\Models\CalendarEvent;


class Entity extends Model
{
    protected $fillable = [
        'types',
        'number',
        'nif',
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
        'nif' => 'encrypted',
        'phone' => 'encrypted',
        'mobile' => 'encrypted',
        'email' => 'encrypted',
        'address' => 'encrypted',
        'observations' => 'encrypted',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

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
}
