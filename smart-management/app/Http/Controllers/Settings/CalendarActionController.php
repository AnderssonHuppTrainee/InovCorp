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
        try {
            CalendarAction::create($request->validated());

            return redirect()->route('calendar-actions.index')
                ->with('success', 'Ação criada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Esta ação já está registada no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao criar ação. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar ação de calendário:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao criar ação. Contacte o suporte.');
        }
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
        try {
            $calendarAction->update($request->validated());

            return redirect()->route('calendar-actions.index')
                ->with('success', 'Ação atualizada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Esta ação já está registada no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao atualizar ação. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar ação de calendário:', [
                'action_id' => $calendarAction->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao atualizar ação. Contacte o suporte.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarAction $calendarAction)
    {
        try {
            if ($calendarAction->calendarEvents()->exists()) {
                return redirect()->back()
                    ->with('error', 'Não é possível eliminar esta ação, pois existem eventos associados.');
            }

            $actionName = $calendarAction->name;
            $calendarAction->delete();

            return redirect()->route('calendar-actions.index')
                ->with('success', "Ação \"{$actionName}\" eliminada com sucesso!");

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Esta ação não pode ser eliminada pois está associada a outros registos.');
            }

            return back()->with('error', 'Erro ao eliminar ação. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar ação de calendário:', [
                'action_id' => $calendarAction->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar ação. Contacte o suporte.');
        }
    }
}

