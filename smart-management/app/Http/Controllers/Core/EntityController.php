<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\Core\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Catalog\Country;
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

        return Inertia::render(
            'entities/Index',
            [
                'entities' => $entities,
                'filters' => $request->only(['search', 'status', 'country_id']),
                'type' => $type,
                'countries' => Country::active()->get(['id', 'name', 'code'])

            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'client');

        // Define os tipos padrão baseado na URL
        $defaultTypes = [$type];

        return Inertia::render(
            'entities/Create',
            [
                'type' => $type,
                'countries' => Country::active()->get(['id', 'name', 'code']),
                'defaultTypes' => $defaultTypes,
            ]
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntityRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use (&$validated) {
                //chama a função de gerar num incremental
                $validated['number'] = Entity::nextNumber();
                Entity::create($validated);
            });

            $redirectType = $validated['types'][0] ?? 'client';

            return redirect()
                ->route('entities.index', ['type' => $redirectType])
                ->with('success', 'Entidade criada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            // Tratamento específico para erros de banco de dados
            if ($e->getCode() === '23000') {
                // Constraint violation (duplicate entry)
                if (str_contains($e->getMessage(), 'tax_number')) {
                    return back()
                        ->withInput()
                        ->with('error', 'Este NIF já está registado no sistema.');
                }
                if (str_contains($e->getMessage(), 'email')) {
                    return back()
                        ->withInput()
                        ->with('error', 'Este email já está registado no sistema.');
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Erro ao criar entidade. Por favor, verifique os dados e tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar entidade:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erro inesperado ao criar entidade. Contacte o suporte.');
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
            $validated = $request->validated();
            $entity->update($validated);

            $redirectType = $validated['types'][0] ?? 'client';

            return redirect()
                ->route('entities.index', ['type' => $redirectType])
                ->with('success', 'Entidade atualizada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            // Tratamento específico para erros de banco de dados
            if ($e->getCode() === '23000') {
                // Constraint violation (duplicate entry)
                if (str_contains($e->getMessage(), 'tax_number')) {
                    return back()
                        ->withInput()
                        ->with('error', 'Este NIF já está registado no sistema.');
                }
                if (str_contains($e->getMessage(), 'email')) {
                    return back()
                        ->withInput()
                        ->with('error', 'Este email já está registado no sistema.');
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar entidade. Por favor, verifique os dados e tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar entidade:', [
                'entity_id' => $entity->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erro inesperado ao atualizar entidade. Contacte o suporte.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        try {
            $entityName = $entity->name;
            $entity->delete();

            return redirect()
                ->route('entities.index')
                ->with('success', "Entidade \"{$entityName}\" eliminada com sucesso!");

        } catch (\Illuminate\Database\QueryException $e) {
            // Constraint violation (foreign key)
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Esta entidade não pode ser eliminada pois está associada a outros registos (propostas, encomendas, etc).');
            }

            return back()->with('error', 'Erro ao eliminar entidade. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar entidade:', [
                'entity_id' => $entity->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar entidade. Contacte o suporte.');
        }
    }


    public function viesCheck(Request $request)
    {
        $request->validate([
            'vat_number' => 'required|string|max:20'
        ]);

        try {
            $vatNumber = $request->vat_number;

            // Remover caracteres não numéricos
            $cleanVat = preg_replace('/[^0-9]/', '', $vatNumber);

            // Verificar formato PT
            if (!preg_match('/^PT\d{9}$/', $vatNumber)) {
                $vatNumber = 'PT' . $cleanVat;
            }

            $client = new \SoapClient(
                "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl",
                ['exceptions' => true]
            );

            $countryCode = substr($vatNumber, 0, 2);
            $vatNumberOnly = substr($vatNumber, 2);

            $result = $client->checkVat([
                'countryCode' => $countryCode,
                'vatNumber' => $vatNumberOnly
            ]);

            return response()->json([
                'valid' => $result->valid,
                'name' => isset($result->name) && trim($result->name) !== '' ? trim($result->name) : null,
                'address' => isset($result->address) && trim($result->address) !== '' ? trim($result->address) : null,
                'country_code' => $result->countryCode,
                'vat_number' => $result->vatNumber,
            ]);

        } catch (\SoapFault $e) {
            return response()->json([
                'valid' => false,
                'error' => 'Serviço VIES indisponível. Tente novamente mais tarde.'
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'error' => 'Erro ao validar NIF: ' . $e->getMessage()
            ], 500);
        }
    }
}
