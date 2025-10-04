<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;


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
        'name' => 'string',
        'encrypted',
        'description' => 'string',
        'tax_rate_id' => 'encrypted',
    ];
}
