<?php

namespace Database\Factories\Core;

use App\Models\Catalog\Country;
use App\Models\Core\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Entity>
 */
class EntityFactory extends Factory
{
    protected $model = Entity::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Criar um country se não existir nenhum
        $country = Country::inRandomOrder()->first() ?? Country::factory()->create();

        return [
            'number' => Entity::nextNumber(),
            'tax_number' => Entity::generatePortugueseNif(),
            'types' => [fake()->randomElement(['client', 'supplier'])],
            'name' => fake()->company(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country_id' => $country->id,
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'website' => 'www.example.pt',
            'email' => fake()->unique()->safeEmail(),
            'gdpr_consent' => true,
            'status' => 'active'
        ];
    }
}
