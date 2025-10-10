<?php

namespace Database\Seeders;

use App\Models\System\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar role de Administrador se não existir
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);

        // Criar utilizador administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin@smartmanagement.pt'],
            [
                'name' => 'Administrador',
                'email' => 'admin@smartmanagement.pt',
                'password' => Hash::make('password'),
                'mobile' => '+351 912 345 678',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole($adminRole);

        // Criar role de Utilizador
        $userRole = Role::firstOrCreate(['name' => 'Utilizador']);

        // Criar utilizador de teste
        $user = User::updateOrCreate(
            ['email' => 'user@smartmanagement.pt'],
            [
                'name' => 'Utilizador Teste',
                'email' => 'user@smartmanagement.pt',
                'password' => Hash::make('password'),
                'mobile' => '+351 912 987 654',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($userRole);

        // Criar mais 5 utilizadores aleatórios
        User::factory(5)->create()->each(function ($user) use ($userRole) {
            $user->assignRole($userRole);
        });
    }
}


