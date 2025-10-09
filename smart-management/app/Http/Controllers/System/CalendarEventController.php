<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalendarEventRequest;
use App\Http\Requests\UpdateCalendarEventRequest;
use App\Models\Core\Entity;
use App\Models\System\Calendar\CalendarAction;
use App\Models\System\Calendar\CalendarEvent;
use App\Models\System\Calendar\CalendarEventType;
use App\Models\System\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventController extends Controller
{
    /**
     * Display the calendar with events.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'entity_id', 'type_id', 'status']);

        // Get events for current user or filtered
        $userId = $filters['user_id'] ?? auth()->id();

        $events = CalendarEvent::query()
            ->with(['entity', 'type', 'action', 'user'])
            ->filter($filters)
            ->get()
            ->map(function ($event) {
                return $event->full_calendar_event;
            });

        return Inertia::render('calendar/Index', [
            'events' => $events,
            'filters' => $filters,
            'users' => User::orderBy('name')->get(['id', 'name']),
            'entities' => Entity::active()->orderBy('name')->get(['id', 'name']),
            'types' => CalendarEventType::where('is_active', true)->get(['id', 'name', 'color']),
            'actions' => CalendarAction::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    /**
     * Get events for a specific date range (for AJAX).
     */
    public function getEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $filters = $request->only(['user_id', 'entity_id']);

        $events = CalendarEvent::query()
            ->with(['entity', 'type', 'action', 'user'])
            ->betweenDates($start, $end)
            ->filter($filters)
            ->get()
            ->map(function ($event) {
                return $event->full_calendar_event;
            });

        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarEventRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        try {
            $event = CalendarEvent::create($validated);

            return back()->with('success', 'Evento criado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar evento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        $calendarEvent->load(['entity', 'type', 'action', 'user']);

        return response()->json([
            'event' => $calendarEvent,
            'shared_users' => $calendarEvent->sharedUsers(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarEventRequest $request, CalendarEvent $calendarEvent)
    {
        $validated = $request->validated();

        try {
            $calendarEvent->update($validated);

            return back()->with('success', 'Evento atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar evento: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEvent $calendarEvent)
    {
        try {
            $calendarEvent->delete();

            return back()->with('success', 'Evento eliminado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar evento: ' . $e->getMessage());
        }
    }
}
