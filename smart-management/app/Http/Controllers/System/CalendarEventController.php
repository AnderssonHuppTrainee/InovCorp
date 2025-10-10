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
        // Se for um update parcial (drag/drop/resize), usar validação mais flexível
        if ($this->isPartialUpdate($request)) {
            return $this->partialUpdate($request, $calendarEvent);
        }

        $validated = $request->validated();

        \Log::info('Updating calendar event', [
            'event_id' => $calendarEvent->id,
            'validated_data' => $validated,
            'request_all' => $request->all()
        ]);

        try {
            $calendarEvent->update($validated);

            \Log::info('Calendar event updated successfully', ['event_id' => $calendarEvent->id]);

            return back()->with('success', 'Evento atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Error updating calendar event', [
                'event_id' => $calendarEvent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Erro ao atualizar evento: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if this is a partial update (drag/drop/resize).
     */
    private function isPartialUpdate($request): bool
    {
        $allFields = ['event_date', 'event_time', 'duration', 'calendar_event_type_id', 'calendar_action_id', 'description', 'status'];
        $providedFields = array_keys($request->all());

        // Se não tem todos os campos obrigatórios, é um update parcial
        foreach (['calendar_event_type_id', 'calendar_action_id', 'description'] as $required) {
            if (!in_array($required, $providedFields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle partial updates (drag/drop/resize).
     */
    private function partialUpdate($request, CalendarEvent $calendarEvent)
    {
        $allowedFields = ['event_date', 'event_time', 'duration', 'status'];
        $data = $request->only($allowedFields);

        // Validação simples para campos parciais
        $rules = [];
        if (isset($data['event_date'])) {
            $rules['event_date'] = ['required', 'date'];
        }
        if (isset($data['event_time'])) {
            $rules['event_time'] = ['required', 'date_format:H:i'];
        }
        if (isset($data['duration'])) {
            $rules['duration'] = ['required', 'integer', 'min:1'];
        }
        if (isset($data['status'])) {
            $rules['status'] = ['required', 'in:scheduled,completed,cancelled'];
        }

        $validated = $request->validate($rules);

        \Log::info('Partial update calendar event', [
            'event_id' => $calendarEvent->id,
            'data' => $validated
        ]);

        try {
            $calendarEvent->update($validated);

            return back()->with('success', 'Evento atualizado!');
        } catch (\Exception $e) {
            \Log::error('Error in partial update', [
                'event_id' => $calendarEvent->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
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
