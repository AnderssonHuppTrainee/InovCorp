<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Catalog\ContactRole;
use App\Models\Core\Contact;
use App\Models\Core\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $contacts = Contact::query()
            ->with(['entity', 'role'])
            ->filter($request->only(['search', 'status', 'entity_id', 'contact_role_id']))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['search', 'status', 'entity_id', 'contact_role_id']),
            'entities' => Entity::active()->orderBy('name')->get(['id', 'name']),
            'roles' => ContactRole::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('contacts/Create', [
            'entities' => Entity::active()->orderBy('name')->get(['id', 'name']),
            'roles' => ContactRole::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated) {
                $validated['number'] = Contact::nextNumber();
                Contact::create($validated);
            });

            return redirect()
                ->route('contacts.index')
                ->with('success', 'Contacto criado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar contacto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $contact->load(['entity', 'role']);

        return Inertia::render('contacts/Show', [
            'contact' => $contact,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        $contact->load(['entity', 'role']);

        return Inertia::render('contacts/Edit', [
            'contact' => $contact,
            'entities' => Entity::active()->orderBy('name')->get(['id', 'name']),
            'roles' => ContactRole::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        try {
            $validated = $request->validated();
            $contact->update($validated);

            return redirect()
                ->route('contacts.index')
                ->with('success', 'Contacto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar contacto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();

            return redirect()
                ->route('contacts.index')
                ->with('success', 'Contacto eliminado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar contacto: ' . $e->getMessage());
        }
    }
}
