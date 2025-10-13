<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\ContactRole;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            ['name' => 'Gerente', 'description' => 'Gerente de conta'],
            ['name' => 'Técnico', 'description' => 'Técnico responsável'],
            ['name' => 'Financeiro', 'description' => 'Responsável financeiro'],
            ['name' => 'Comercial', 'description' => 'Contato comercial'],
        ];

        $role = fake()->randomElement($roles);

        return [
            'name' => $role['name'],
            'description' => $role['description'],
            'is_active' => true,
        ];
    }
}

