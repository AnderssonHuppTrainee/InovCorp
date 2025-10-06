<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContactRole;
use App\Models\Entity;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactsFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'entity_id',
        'name',
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



}
