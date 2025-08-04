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
            'bibliography' => Crypt::encryptString($this->faker->paragraph()),
            'cover_image' => $this->faker->imageUrl(640, 480, 'book'),
            'price' => Crypt::encryptString($this->faker->randomFloat(2, 10, 100)),
        ];
    }

    // configuracao relacionamento com autores
    /*public function configure()
    {
        return $this->afterCreating(function (\App\Models\Book $book) {
            $book->authors()->attach(
                Author::factory()->count(rand(1, 3))->create()
            );
        });
    }*/
}
