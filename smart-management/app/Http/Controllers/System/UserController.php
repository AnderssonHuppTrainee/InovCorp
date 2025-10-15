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

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'email')) {
                    return back()->withInput()->with('error', 'Este email já está registado no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao criar utilizador. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar utilizador:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao criar utilizador. Contacte o suporte.');
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

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'email')) {
                    return back()->withInput()->with('error', 'Este email já está registado no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao atualizar utilizador. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar utilizador:', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao atualizar utilizador. Contacte o suporte.');
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

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Este utilizador não pode ser eliminado pois está associado a outros registos (ordens de trabalho, etc).');
            }

            return back()->with('error', 'Erro ao eliminar utilizador. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar utilizador:', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar utilizador. Contacte o suporte.');
        }
    }
}
