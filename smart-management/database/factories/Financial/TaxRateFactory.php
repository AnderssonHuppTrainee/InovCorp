<?php

namespace Database\Factories\Financial;

use App\Models\Financial\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Financial\TaxRate>
 */
class TaxRateFactory extends Factory
{
    protected $model = TaxRate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Normal', 'Intermédia', 'Reduzida', 'Isenta']),
            'rate' => fake()->randomElement([23, 13, 6, 0]),
            'is_active' => fake()->boolean(90),
        ];
    }
}



