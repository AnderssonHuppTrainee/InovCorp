<?php

namespace App\Models\System\Calendar;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\Calendar\CalendarEvent;

class CalendarEventType extends Model
{
    protected $fillable = ['name', 'color', 'is_active'];

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'calendar_event_type_id');
    }
}
