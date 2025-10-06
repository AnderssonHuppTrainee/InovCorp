<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CalendarEvent;

class CalendarAction extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'calendar_action_id');
    }
}
