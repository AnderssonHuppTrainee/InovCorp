<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProposalItem;
use App\Models\Entity;
class Proposal extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalsFactory> */
    use HasFactory;


    protected $fillable = [
        'number',
        'proposal_date',
        'client_id',
        'validity_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'number' => 'encrypted',
        'proposal_date' => 'date',
        'validity_date' => 'date'
    ];
    public function client()
    {
        return $this->belongsTo(Entity::class, 'client_id');
    }
    public function items()
    {
        return $this->hasMany(ProposalItem::class);
    }

}
