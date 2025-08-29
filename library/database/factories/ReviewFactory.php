<?php

namespace Database\Factories;

use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'book_request_id' => BookRequest::inRandomOrder()->value('id'),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(12),
            'status' => $this->faker->randomElement(['suspended', 'active', 'refused']),
        ];
    }
}
