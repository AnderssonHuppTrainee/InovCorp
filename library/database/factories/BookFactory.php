<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Publisher;
use Illuminate\Support\Facades\Crypt;
use App\Models\Author;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isbn' => $this->faker->isbn13(),
            'name' => $this->faker->sentence(3),
            'publisher_id' => Publisher::factory(),
            'bibliography' => $this->faker->paragraph(),
            'cover_image' => $this->faker->imageUrl(640, 480, 'book'),
            'price' => $this->faker->randomFloat(2, 10, 50),
            'stock' => $this->faker->numberBetween(1, 20),
        ];
    }


}
