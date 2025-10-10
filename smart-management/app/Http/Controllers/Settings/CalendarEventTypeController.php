<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\System\Calendar\CalendarEventType;
use App\Http\Requests\StoreCalendarEventTypeRequest;
use App\Http\Requests\UpdateCalendarEventTypeRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CalendarEventType::query();

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->status !== null) {
            $query->where('is_active', $request->status === 'active');
        }

        $calendarEventTypes = $query->orderBy('name')->paginate(10);

        return Inertia::render('settings/calendar-event-types/Index', [
            'calendarEventTypesData' => $calendarEventTypes,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('settings/calendar-event-types/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarEventTypeRequest $request)
    {
        CalendarEventType::create($request->validated());

        return redirect()->route('calendar-event-types.index')
            ->with('success', 'Tipo de evento criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEventType $calendarEventType)
    {
        $calendarEventType->load('calendarEvents');

        return Inertia::render('settings/calendar-event-types/Show', [
            'calendarEventType' => $calendarEventType,
            'eventsCount' => $calendarEventType->calendarEvents()->count(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarEventType $calendarEventType)
    {
        return Inertia::render('settings/calendar-event-types/Edit', [
            'calendarEventType' => $calendarEventType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarEventTypeRequest $request, CalendarEventType $calendarEventType)
    {
        $calendarEventType->update($request->validated());

        return redirect()->route('calendar-event-types.index')
            ->with('success', 'Tipo de evento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEventType $calendarEventType)
    {
        if ($calendarEventType->calendarEvents()->exists()) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar este tipo, pois existem eventos associados.');
        }

        $calendarEventType->delete();

        return redirect()->route('calendar-event-types.index')
            ->with('success', 'Tipo de evento eliminado com sucesso.');
    }
}

