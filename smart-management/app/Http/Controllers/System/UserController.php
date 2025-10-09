<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\System\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->with('roles')
            ->filter($request->only(['search', 'is_active', 'role']))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $roles = Role::orderBy('name')->get(['id', 'name']);

        return Inertia::render('access-management/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'is_active', 'role']),
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return Inertia::render('access-management/users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        try {
            $user = DB::transaction(function () use ($validated) {
                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'mobile' => $validated['mobile'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'is_active' => $validated['is_active'] ?? true,
                ];

                $user = User::create($userData);

                // Assign roles
                if (!empty($validated['roles'])) {
                    $user->syncRoles($validated['roles']);
                }

                return $user;
            });

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Utilizador criado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar utilizador: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions', 'workOrders', 'calendarEvents']);

        return Inertia::render('access-management/users/Show', [
            'user' => $user,
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return Inertia::render('access-management/users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $user->roles->pluck('name')->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $user) {
                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'mobile' => $validated['mobile'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                ];

                // Only update password if provided
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $user->update($userData);

                // Sync roles
                if (isset($validated['roles'])) {
                    $user->syncRoles($validated['roles']);
                }
            });

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Utilizador atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar utilizador: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting self
            if ($user->id === auth()->id()) {
                return back()->with('error', 'Não pode eliminar o seu próprio utilizador.');
            }

            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'Utilizador eliminado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar utilizador: ' . $e->getMessage());
        }
    }
}
