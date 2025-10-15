<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->withCount('users')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('access-management/roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'other';
        });

        return Inertia::render('access-management/roles/Create', [
            'permissionsGrouped' => $permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        try {
            $role = DB::transaction(function () use ($validated) {
                $role = Role::create([
                    'name' => $validated['name'],
                    'guard_name' => 'web',
                ]);

                // Assign permissions
                if (!empty($validated['permissions'])) {
                    $role->syncPermissions($validated['permissions']);
                }

                return $role;
            });

            return redirect()
                ->route('roles.show', $role)
                ->with('success', 'Grupo de permissões criado com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Este grupo de permissões já existe no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao criar grupo. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar grupo de permissões:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao criar grupo. Contacte o suporte.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        return Inertia::render('access-management/roles/Show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $role->load('permissions');

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'other';
        });

        return Inertia::render('access-management/roles/Edit', [
            'role' => $role,
            'permissionsGrouped' => $permissions,
            'rolePermissions' => $role->permissions->pluck('name')->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $role) {
                $role->update([
                    'name' => $validated['name'],
                ]);

                // Sync permissions
                if (isset($validated['permissions'])) {
                    $role->syncPermissions($validated['permissions']);
                } else {
                    $role->syncPermissions([]);
                }
            });

            return redirect()
                ->route('roles.show', $role)
                ->with('success', 'Grupo de permissões atualizado com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Este grupo de permissões já existe no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao atualizar grupo. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar grupo de permissões:', [
                'role_id' => $role->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao atualizar grupo. Contacte o suporte.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            // Check if role has users
            if ($role->users()->count() > 0) {
                return back()->with('error', 'Não é possível eliminar um grupo com utilizadores associados.');
            }

            $role->delete();

            return redirect()
                ->route('roles.index')
                ->with('success', 'Grupo de permissões eliminado com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Este grupo não pode ser eliminado pois está associado a outros registos.');
            }

            return back()->with('error', 'Erro ao eliminar grupo. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar grupo de permissões:', [
                'role_id' => $role->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar grupo. Contacte o suporte.');
        }
    }

    /**
     * Sync permissions for all existing routes.
     */
    public function syncPermissions()
    {
        try {
            $routes = \Route::getRoutes();
            $permissions = [];

            foreach ($routes as $route) {
                $name = $route->getName();
                if ($name && !str_starts_with($name, 'debugbar') && !str_starts_with($name, 'sanctum')) {
                    $permissions[] = $name;
                }
            }

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }

            return back()->with('success', count($permissions) . ' permissões sincronizadas!');

        } catch (\Exception $e) {
            \Log::error('Erro ao sincronizar permissões:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro ao sincronizar permissões. Contacte o suporte.');
        }
    }
}





