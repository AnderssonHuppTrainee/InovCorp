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
        try {
            CalendarEventType::create($request->validated());

            return redirect()->route('calendar-event-types.index')
                ->with('success', 'Tipo de evento criado com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Este tipo de evento já está registado no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao criar tipo de evento. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar tipo de evento:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao criar tipo de evento. Contacte o suporte.');
        }
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
        try {
            $calendarEventType->update($request->validated());

            return redirect()->route('calendar-event-types.index')
                ->with('success', 'Tipo de evento atualizado com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Este tipo de evento já está registado no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao atualizar tipo de evento. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar tipo de evento:', [
                'type_id' => $calendarEventType->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao atualizar tipo de evento. Contacte o suporte.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEventType $calendarEventType)
    {
        try {
            if ($calendarEventType->calendarEvents()->exists()) {
                return redirect()->back()
                    ->with('error', 'Não é possível eliminar este tipo, pois existem eventos associados.');
            }

            $typeName = $calendarEventType->name;
            $calendarEventType->delete();

            return redirect()->route('calendar-event-types.index')
                ->with('success', "Tipo de evento \"{$typeName}\" eliminado com sucesso!");

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Este tipo de evento não pode ser eliminado pois está associado a outros registos.');
            }

            return back()->with('error', 'Erro ao eliminar tipo de evento. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar tipo de evento:', [
                'type_id' => $calendarEventType->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar tipo de evento. Contacte o suporte.');
        }
    }
}

