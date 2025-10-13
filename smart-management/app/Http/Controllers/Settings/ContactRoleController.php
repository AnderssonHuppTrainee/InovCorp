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
        ContactRole::create($request->validated());

        return redirect()->route('contact-roles.index')
            ->with('success', 'Função criada com sucesso.');
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
        $contactRole->update($request->validated());

        return redirect()->route('contact-roles.index')
            ->with('success', 'Função atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactRole $contactRole)
    {
        if ($contactRole->contacts()->exists()) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar esta função, pois existem contactos associados.');
        }

        $contactRole->delete();

        return redirect()->route('contact-roles.index')
            ->with('success', 'Função eliminada com sucesso.');
    }
}

