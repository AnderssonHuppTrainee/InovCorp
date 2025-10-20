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

            if ($e->getCode() === '23000') {

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
        //load antes do render para garatir q venha
        $entity->load('country');

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

            if ($e->getCode() === '23000') {

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
            $vatNumber = strtoupper(trim($request->vat_number));

            // Remover espaços e caracteres inválidos
            $vatNumber = preg_replace('/\s+/', '', $vatNumber);

            // Extrair prefixo do país (2 primeiras letras)
            $countryCode = substr($vatNumber, 0, 2);
            $vatNumberOnly = substr($vatNumber, 2);

            // Lista de países suportados pela UE no VIES
            $euCountries = [
                'AT',
                'BE',
                'BG',
                'CY',
                'CZ',
                'DE',
                'DK',
                'EE',
                'EL',
                'ES',
                'FI',
                'FR',
                'HR',
                'HU',
                'IE',
                'IT',
                'LT',
                'LU',
                'LV',
                'MT',
                'NL',
                'PL',
                'PT',
                'RO',
                'SE',
                'SI',
                'SK'
            ];

            // Validar se o prefixo é de um país da UE
            if (!in_array($countryCode, $euCountries)) {
                return response()->json([
                    'valid' => false,
                    'error' => 'País não suportado pelo VIES. Utilize um NIF da União Europeia (ex: DE123456789).'
                ], 422);
            }

            // Criar o cliente SOAP
            $client = new \SoapClient(
                "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl",
                ['exceptions' => true]
            );

            // Fazer a chamada ao VIES
            $result = $client->checkVat([
                'countryCode' => $countryCode,
                'vatNumber' => $vatNumberOnly
            ]);

            // Retornar o resultado padronizado
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
            \Log::error('Erro ao validar VAT no VIES', [
                'message' => $e->getMessage(),
                'input' => $request->vat_number
            ]);

            return response()->json([
                'valid' => false,
                'error' => 'Erro ao validar VAT: ' . $e->getMessage()
            ], 500);
        }
    }

}
