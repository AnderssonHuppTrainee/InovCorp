<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProposalItem;

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
        'encrypted',
        'observations' => 'string',
        'encrypted',
    ];

    public function proposalItems()
    {
        return $this->hasMany(ProposalItem::class);
    }
}
