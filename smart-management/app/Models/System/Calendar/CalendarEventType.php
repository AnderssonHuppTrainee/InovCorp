<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CalendarEvent;

class CalendarEventType extends Model
{
    protected $fillable = ['name', 'color', 'is_active'];

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'calendar_event_type_id');
    }
}
