<?php

namespace Database\Factories;

use App\Models\BookRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Book;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookRequest>
 */
class BookRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {


        $requestDate = $this->faker->dateTimeBetween('-2 months', 'now');
        $expectedReturnDate = (clone $requestDate)->modify('+5 days'); // prazo padrão
        $returnedDate = $this->faker->boolean(70) // 70% de chance de devolver
            ? $this->faker->dateTimeBetween($expectedReturnDate, '+15 days') // atraso
            : null;

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'book_id' => Book::inRandomOrder()->value('id'),
            'request_date' => $requestDate,
            'expected_return_date' => $expectedReturnDate,
            'returned_date' => $returnedDate,
            'actual_days' => $returnedDate
                ? $requestDate->diff($returnedDate)->days
                : null,
            'admin_confirmed_return_date' => $returnedDate,
            'book_condition' => $this->faker->randomElement(['Excellent', 'Good', 'Bad', 'Damaged', 'Lost']),
            'status' => $this->faker->randomElement(['approved', 'pending', 'pending_returned', 'returned']),
        ];
    }
}
