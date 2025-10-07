<?php

namespace App\Models\System\Calendar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Entity;
use App\Models\System\Calendar\CalendarEventType;
use App\Models\System\Calendar\CalendarAction;
use App\Models\System\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEvent extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarFactory> */
    use HasFactory;
    use SoftDeletes;

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
