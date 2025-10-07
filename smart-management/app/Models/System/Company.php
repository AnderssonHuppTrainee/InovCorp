<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'logo',
        'name',
        'address',
        'postal_code',
        'city',
        'tax_number',
        'phone',
        'email',
        'website'
    ];

    protected $casts = [
        'name' => 'encrypted',
        'address' => 'encrypted',
        'postal_code' => 'encrypted',
        'tax_number' => 'encrypted',
        'phone' => 'encrypted',
        'email' => 'encrypted',
    ];
}
