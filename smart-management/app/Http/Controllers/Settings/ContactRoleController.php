<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Catalog\ContactRole;
use App\Http\Requests\StoreContactRoleRequest;
use App\Http\Requests\UpdateContactRoleRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContactRole::query();

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

        $contactRoles = $query->orderBy('name')->paginate(10);

        return Inertia::render('settings/contact-roles/Index', [
            'contactRolesData' => $contactRoles,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('settings/contact-roles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRoleRequest $request)
    {
        try {
            ContactRole::create($request->validated());

            return redirect()->route('contact-roles.index')
                ->with('success', 'Função criada com sucesso!');
                
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Esta função já está registada no sistema.');
                }
            }
            
            return back()->withInput()->with('error', 'Erro ao criar função. Por favor, verifique os dados.');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao criar função de contacto:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Erro inesperado ao criar função. Contacte o suporte.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactRole $contactRole)
    {
        $contactRole->load('contacts');

        return Inertia::render('settings/contact-roles/Show', [
            'contactRole' => $contactRole,
            'contactsCount' => $contactRole->contacts()->count(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactRole $contactRole)
    {
        return Inertia::render('settings/contact-roles/Edit', [
            'contactRole' => $contactRole,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRoleRequest $request, ContactRole $contactRole)
    {
        try {
            $contactRole->update($request->validated());

            return redirect()->route('contact-roles.index')
                ->with('success', 'Função atualizada com sucesso!');
                
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Esta função já está registada no sistema.');
                }
            }
            
            return back()->withInput()->with('error', 'Erro ao atualizar função. Por favor, verifique os dados.');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar função de contacto:', [
                'role_id' => $contactRole->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Erro inesperado ao atualizar função. Contacte o suporte.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactRole $contactRole)
    {
        try {
            if ($contactRole->contacts()->exists()) {
                return redirect()->back()
                    ->with('error', 'Não é possível eliminar esta função, pois existem contactos associados.');
            }

            $roleName = $contactRole->name;
            $contactRole->delete();

            return redirect()->route('contact-roles.index')
                ->with('success', "Função \"{$roleName}\" eliminada com sucesso!");
                
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Esta função não pode ser eliminada pois está associada a outros registos.');
            }
            
            return back()->with('error', 'Erro ao eliminar função. Por favor, tente novamente.');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar função de contacto:', [
                'role_id' => $contactRole->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Erro inesperado ao eliminar função. Contacte o suporte.');
        }
    }
}

