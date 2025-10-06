<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProposalItem;
use App\Models\TaxRate;
use App\Models\OrderItem;

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
    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function ordersItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function proposalItems()
    {
        return $this->hasMany(ProposalItem::class);
    }
}
