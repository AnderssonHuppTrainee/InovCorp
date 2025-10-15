<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderRequest;
use App\Models\Core\Entity;
use App\Models\Core\WorkOrder;
use App\Models\System\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $workOrders = WorkOrder::query()
            ->with(['client', 'assignedUser'])
            ->filter($request->only(['search', 'status', 'priority', 'client_id', 'assigned_to']))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('work-orders/Index', [
            'workOrders' => $workOrders,
            'filters' => $request->only(['search', 'status', 'priority', 'client_id', 'assigned_to']),
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('work-orders/Create', [
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkOrderRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use (&$validated) {
                $validated['number'] = WorkOrder::nextNumber();
                WorkOrder::create($validated);
            });

            return redirect()
                ->route('work-orders.index')
                ->with('success', 'Ordem de trabalho criada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'client_id')) {
                    return back()->withInput()->with('error', 'Cliente inválido ou inexistente.');
                }
                if (str_contains($e->getMessage(), 'assigned_to')) {
                    return back()->withInput()->with('error', 'Utilizador atribuído inválido ou inexistente.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao criar ordem de trabalho. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar ordem de trabalho:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao criar ordem de trabalho. Contacte o suporte.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['client', 'assignedUser']);

        return Inertia::render('work-orders/Show', [
            'workOrder' => $workOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrder $workOrder)
    {
        $workOrder->load(['client', 'assignedUser']);

        return Inertia::render('work-orders/Edit', [
            'workOrder' => $workOrder,
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder)
    {
        try {
            $validated = $request->validated();
            $workOrder->update($validated);

            return redirect()
                ->route('work-orders.index')
                ->with('success', 'Ordem de trabalho atualizada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'client_id')) {
                    return back()->withInput()->with('error', 'Cliente inválido ou inexistente.');
                }
                if (str_contains($e->getMessage(), 'assigned_to')) {
                    return back()->withInput()->with('error', 'Utilizador atribuído inválido ou inexistente.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao atualizar ordem de trabalho. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar ordem de trabalho:', [
                'work_order_id' => $workOrder->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao atualizar ordem de trabalho. Contacte o suporte.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkOrder $workOrder)
    {
        try {
            $workOrderNumber = $workOrder->number;
            $workOrder->delete();

            return redirect()
                ->route('work-orders.index')
                ->with('success', "Ordem de trabalho \"{$workOrderNumber}\" eliminada com sucesso!");

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Esta ordem de trabalho não pode ser eliminada pois está associada a outros registos (faturas, etc).');
            }

            return back()->with('error', 'Erro ao eliminar ordem de trabalho. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar ordem de trabalho:', [
                'work_order_id' => $workOrder->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar ordem de trabalho. Contacte o suporte.');
        }
    }
}
