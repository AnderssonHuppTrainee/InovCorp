<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'client');

        $entities = Entity::query()
            ->when($type === 'client', function ($query) {
                return $query->clients();
            })
            ->when($type === 'supplier', function ($query) {
                return $query->suppliers();
            })
            ->with('country')
            ->filter($request->only(['search', 'status', 'country_id']))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('entities/Index', [
            'entities' => $entities,
            'filters' => $request->only(['search', 'status', 'country_id']),
            'type' => $type,
            'countries' => Country::active()->get(['id', 'name', 'code'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'client');

        return Inertia::render('entities/Create.vue', [
            'type' => $type,
            'countries' => Country::active()->get(['id', 'name', 'code']),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntityRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::transaction(function () use ($validated) {
                //chama a função de gerar num incremental
                $validated['number'] = Entity::nextNumber();
                Entity::create($validated);
            });

            $redirectType = $validated['types'][0] ?? 'client';

            return redirect()
                ->route('entities.index', ['type' => $redirectType])
                ->with('success', 'Entidade criada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar entidade: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        return Inertia::render('entities/Show', [
            'entity' => $entity,
            'type' => $entity->types[0] ?? 'client'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entity $entity)
    {
        return Inertia::render('entities/Edit', [
            'entity' => $entity->load('country'),
            'countries' => Country::active()->get(['id', 'name', 'code']),
            'type' => $entity->types[0] ?? 'client'
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityRequest $request, Entity $entity)
    {
        try {
            $entity->update($request->validated());

            $redirectType = $validated['types'][0] ?? 'client';

            return redirect()
                ->route('entities.index', ['type' => $redirectType])
                ->with('success', 'Entidade atualizada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar entidade: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        try {

            $entity->delete();

            return redirect()
                ->route('entities.index')
                ->with('success', 'Entidade eliminada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar entidade: ' . $e->getMessage());
        }
    }
}
