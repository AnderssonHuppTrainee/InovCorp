<?php

namespace Database\Factories\Core;

use App\Models\Core\Article;
use App\Models\Financial\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'ART' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'tax_rate_id' => TaxRate::inRandomOrder()->first()?->id ?? TaxRate::factory(),
            'observations' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}



