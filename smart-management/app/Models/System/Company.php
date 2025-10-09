<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
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

    // Singleton pattern - sempre retorna/cria uma única empresa
    public static function get()
    {
        return static::first() ?? static::create([
            'name' => config('app.name'),
            'address' => '',
            'postal_code' => '',
            'city' => '',
            'tax_number' => '',
            'phone' => '',
            'email' => '',
            'website' => '',
        ]);
    }

    public static function generatePortugueseNif(): string
    {
        // Prefixos válidos para NIF: 1, 2, 3, 5, 6, 8
        $prefixes = [1, 2, 3, 5, 6, 8];
        $nif = (string) fake()->randomElement($prefixes);

        // Gerar os 8 restantes dígitos aleatórios
        for ($i = 0; $i < 8; $i++) {
            $nif .= fake()->randomDigit();
        }

        return $nif;
    }
}
