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
        return [
            'number' => fake()->unique()->numerify('######'),
            'tax_number' => Entity::generatePortugueseNif(),
            'types' => [fake()->randomElement(['client', 'supplier'])],
            'name' => fake()->company(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country_id' => Country::inRandomOrder()->first()->id,
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'website' => 'www.example.pt',
            'email' => fake()->unique()->safeEmail(),
            'gdpr_consent' => true,
            'status' => 'active'
        ];
    }
}
