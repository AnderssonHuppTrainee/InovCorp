<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Sincronizando permissões...');

        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Obter todas as rotas nomeadas
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return $route->getName() && !str_starts_with($route->getName(), 'sanctum.');
        });

        // Agrupar rotas por recurso
        $permissions = [];
        foreach ($routes as $route) {
            $name = $route->getName();

            // Extrair o nome do recurso e a ação
            if (preg_match('/^(.+)\.(index|create|store|show|edit|update|destroy)$/', $name, $matches)) {
                $resource = $matches[1];
                $action = $matches[2];

                $permissions[] = $name;
            } elseif (!in_array($name, ['home', 'dashboard', 'login', 'logout', 'register'])) {
                $permissions[] = $name;
            }
        }

        // Criar permissões
        foreach (array_unique($permissions) as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }

        // Criar roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $userRole = Role::firstOrCreate(['name' => 'Utilizador']);
        $managerRole = Role::firstOrCreate(['name' => 'Gestor']);

        // Administrador tem todas as permissões
        $adminRole->syncPermissions(Permission::all());

        // Utilizador tem permissões limitadas (view e show)
        $userPermissions = Permission::where('name', 'like', '%.index')
            ->orWhere('name', 'like', '%.show')
            ->get();
        $userRole->syncPermissions($userPermissions);

        // Gestor tem permissões de CRUD exceto delete
        $managerPermissions = Permission::whereNotIn('name', function ($query) {
            $query->select('name')
                ->from('permissions')
                ->where('name', 'like', '%.destroy');
        })->get();
        $managerRole->syncPermissions($managerPermissions);

        $this->command->info("✅ " . count($permissions) . " permissões criadas");
        $this->command->info("✅ 3 roles criadas (Administrador, Gestor, Utilizador)");
    }
}

