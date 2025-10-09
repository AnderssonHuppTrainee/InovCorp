<?php

namespace Database\Factories\Catalog;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Catalog\ContactRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog\ContactRole>
 */
class ContactRoleFactory extends Factory
{
    protected $model = ContactRole::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $roles = [
            'Diretor Geral',
            'Gestor de Compras',
            'Gestor Comercial',
            'Assistente Administrativo',
            'Responsável Financeiro',
            'Técnico de Suporte',
            'Gestor de Projeto',
            'Assistente de Marketing',
            'Consultor Técnico',
            'CEO',
            'CFO',
            'COO',
            'Diretor de Recursos Humanos',
            'Engenheiro de Produção',
        ];

        return [
            'name' => fake()->randomElement($roles),
            'description' => fake()->sentence(8),
            'is_active' => true,
        ];
    }
}
