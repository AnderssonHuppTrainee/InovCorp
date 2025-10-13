<?php

namespace Database\Seeders;

use App\Models\Catalog\ContactRole;
use Illuminate\Database\Seeder;

class ContactRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'CEO', 'description' => 'Chief Executive Officer'],
            ['name' => 'CFO', 'description' => 'Chief Financial Officer'],
            ['name' => 'CTO', 'description' => 'Chief Technology Officer'],
            ['name' => 'Diretor Geral', 'description' => 'Diretor Geral da empresa'],
            ['name' => 'Diretor Comercial', 'description' => 'Responsável pela área comercial'],
            ['name' => 'Diretor Financeiro', 'description' => 'Responsável pela área financeira'],
            ['name' => 'Gestor de Projeto', 'description' => 'Gestão de projetos'],
            ['name' => 'Gestor de Compras', 'description' => 'Responsável pelas compras'],
            ['name' => 'Técnico', 'description' => 'Técnico especializado'],
            ['name' => 'Administrativo', 'description' => 'Funções administrativas'],
            ['name' => 'Comercial', 'description' => 'Área comercial e vendas'],
            ['name' => 'Contabilista', 'description' => 'Responsável pela contabilidade'],
        ];

        foreach ($roles as $role) {
            ContactRole::updateOrCreate(
                ['name' => $role['name']],
                array_merge($role, ['is_active' => true])
            );
        }
    }
}



