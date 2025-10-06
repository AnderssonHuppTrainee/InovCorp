<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProposalItem;
use App\Models\Entity;
use App\Models\Order;
use App\Models\DigitalArchive;
class Proposal extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalsFactory> */
    use HasFactory;
    use SoftDeletes;


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
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function documents()
    {
        return $this->morphMany(DigitalArchive::class, 'archivable')
            ->where('document_type', 'proposal_pdf');
    }

    public function generatePdf()
    {
        $pdfPath = "proposals/{$this->number}.pdf";

        // criar registro
        return DigitalArchive::create([
            'name' => "Proposta {$this->number}",
            'file_name' => "proposta-{$this->number}.pdf",
            'file_path' => $pdfPath,
            'document_type' => 'proposal_pdf',
            'archivable_id' => $this->id,
            'archivable_type' => self::class,
            'uploaded_by' => auth()->id()
        ]);
    }

}
