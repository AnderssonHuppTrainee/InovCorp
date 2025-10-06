<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Proposal;
use App\Models\Article;
use App\Models\Entity;

class ProposalItem extends Model
{

    protected $fillable = [
        'proposal_id',
        'article_id',
        'supplier_id',
        'quantity',
        'unit_price',
        'cost_price',
        'notes'
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
    //pertece a um artigo
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    //pertence a um fornecedor
    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }
}
