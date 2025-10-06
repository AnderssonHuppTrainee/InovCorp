<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Proposal;

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
}
