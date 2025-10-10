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
        'event_date' => 'date:Y-m-d',
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

    public function sharedUsers()
    {
        if (!$this->shared_with) {
            return collect([]);
        }

        return User::whereIn('id', $this->shared_with)->get();
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereJsonContains('shared_with', $userId);
        });
    }

    public function scopeForEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('event_date', [$start, $end]);
    }

    public function scopeFilter($query, array $filters = [])
    {
        $query->when($filters['user_id'] ?? null, function ($query, $userId) {
            $query->forUser($userId);
        })->when($filters['entity_id'] ?? null, function ($query, $entityId) {
            $query->where('entity_id', $entityId);
        })->when($filters['type_id'] ?? null, function ($query, $typeId) {
            $query->where('calendar_event_type_id', $typeId);
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        });
    }

    // Acessor para FullCalendar
    public function getFullCalendarEventAttribute()
    {
        // Garantir que event_date está no formato Y-m-d
        $eventDateStr = $this->event_date instanceof \Carbon\Carbon
            ? $this->event_date->format('Y-m-d')
            : $this->event_date;

        // Garantir que event_time está no formato H:i (apenas hora)
        // Pode vir como string "09:30" ou como datetime object
        if ($this->event_time instanceof \Carbon\Carbon) {
            $eventTimeStr = $this->event_time->format('H:i');
        } else {
            // Extrair apenas H:i se vier como datetime string
            $eventTimeStr = substr($this->event_time, 0, 5);
        }

        $startDateTime = \Carbon\Carbon::parse($eventDateStr . ' ' . $eventTimeStr);
        $endDateTime = $startDateTime->copy()->addMinutes($this->duration);

        // Garantir que type existe
        $backgroundColor = $this->type?->color ?? '#3b82f6';
        $borderColor = $this->type?->color ?? '#3b82f6';

        return [
            'id' => $this->id,
            'title' => $this->description,
            'start' => $startDateTime->toIso8601String(),
            'end' => $endDateTime->toIso8601String(),
            'backgroundColor' => $backgroundColor,
            'borderColor' => $borderColor,
            'extendedProps' => [
                'entity_id' => $this->entity_id,
                'entity_name' => $this->entity?->name,
                'type_name' => $this->type?->name,
                'action_name' => $this->action?->name,
                'status' => $this->status,
                'knowledge' => $this->knowledge,
            ],
        ];
    }
}
