<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\System\ContactRole;
use App\Models\Core\Entity;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactsFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'entity_id',
        'first_name',
        'last_name',
        'contact_role_id',
        'phone',
        'mobile',
        'email',
        'gdpr_consent',
        'observations',
        'status'
    ];

    protected $casts = [
        'gdpr_consent' => 'boolean',
        'status' => 'string',
        'phone' => 'encrypted',
        'mobile' => 'encrypted',
        'email' => 'encrypted',
        'observations' => 'encrypted'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function role()
    {
        return $this->belongsTo(ContactRole::class, 'contact_role_id');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFilter($query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        })->when($filters['entity_id'] ?? null, function ($query, $entityId) {
            $query->where('entity_id', $entityId);
        })->when($filters['contact_role_id'] ?? null, function ($query, $roleId) {
            $query->where('contact_role_id', $roleId);
        });
    }


    public static function nextNumber(): string
    {
        $lastNumber = static::max('number');
        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
