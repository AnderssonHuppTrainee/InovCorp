<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Entity;

class WorkOrder extends Model
{
    /** @use HasFactory<\Database\Factories\WorkOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'title',
        'description',
        'client_id',
        'assigned_to',
        'priority',
        'start_date',
        'end_date',
        'status'
    ];
    protected $casts = [
        'number' => 'encrypted',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function client()
    {
        return $this->belongsTo(Entity::class, 'client_id');
    }
    //pertecem a um user
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

}
