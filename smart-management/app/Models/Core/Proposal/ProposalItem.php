<?php

namespace App\Models\Core\Proposal;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Article;
use App\Models\Core\Entity;

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
