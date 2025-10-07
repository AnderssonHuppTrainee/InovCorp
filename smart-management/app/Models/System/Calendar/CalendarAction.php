<?php

namespace App\Models\System\Calendar;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\Calendar\CalendarEvent;

class CalendarAction extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'calendar_action_id');
    }
}
