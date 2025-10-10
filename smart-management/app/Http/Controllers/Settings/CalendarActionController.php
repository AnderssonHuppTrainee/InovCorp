<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\System\Calendar\CalendarAction;
use App\Http\Requests\StoreCalendarActionRequest;
use App\Http\Requests\UpdateCalendarActionRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CalendarAction::query();

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== null) {
            $query->where('is_active', $request->status === 'active');
        }

        $calendarActions = $query->orderBy('name')->paginate(10);

        return Inertia::render('settings/calendar-actions/Index', [
            'calendarActionsData' => $calendarActions,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('settings/calendar-actions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarActionRequest $request)
    {
        CalendarAction::create($request->validated());

        return redirect()->route('calendar-actions.index')
            ->with('success', 'Ação criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarAction $calendarAction)
    {
        $calendarAction->load('calendarEvents');

        return Inertia::render('settings/calendar-actions/Show', [
            'calendarAction' => $calendarAction,
            'eventsCount' => $calendarAction->calendarEvents()->count(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarAction $calendarAction)
    {
        return Inertia::render('settings/calendar-actions/Edit', [
            'calendarAction' => $calendarAction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarActionRequest $request, CalendarAction $calendarAction)
    {
        $calendarAction->update($request->validated());

        return redirect()->route('calendar-actions.index')
            ->with('success', 'Ação atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarAction $calendarAction)
    {
        if ($calendarAction->calendarEvents()->exists()) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar esta ação, pois existem eventos associados.');
        }

        $calendarAction->delete();

        return redirect()->route('calendar-actions.index')
            ->with('success', 'Ação eliminada com sucesso.');
    }
}

