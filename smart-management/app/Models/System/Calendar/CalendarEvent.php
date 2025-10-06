<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;
use App\Models\CalendarEventType;
use App\Models\CalendarAction;
use App\Models\User;

class CalendarEvent extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarFactory> */
    use HasFactory;

    protected $fillable = [
        'event_date',
        'event_time',
        'duration',
        'shared_with',
        'knowledge',
        'entity_id',
        'calendar_event_type_id',
        'calendar_action_id',
        'description',
        'user_id',
        'status'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime',
        'shared_with' => 'array',
        'knowledge' => 'boolean'
    ];


    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function type()
    {
        return $this->belongsTo(CalendarEventType::class, 'calendar_event_type_id');
    }

    public function action()
    {
        return $this->belongsTo(CalendarAction::class, 'calendar_action_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
