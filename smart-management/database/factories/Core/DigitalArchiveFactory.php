<?php

namespace Database\Factories\Core;

use App\Models\Core\DigitalArchive;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\DigitalArchive>
 */
class DigitalArchiveFactory extends Factory
{
    protected $model = DigitalArchive::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentTypes = ['Invoice', 'Contract', 'Report', 'Certificate', 'Other'];

        return [
            'name' => fake()->words(3, true) . '.' . fake()->fileExtension(),
            'file_path' => 'test/dummy-file.pdf',
            'file_size' => fake()->numberBetween(10000, 5000000),
            'mime_type' => 'application/pdf',
            'description' => fake()->optional()->sentence(),
            'document_type' => fake()->randomElement($documentTypes),
            'visibility' => fake()->randomElement(['public', 'private']),
            'expires_at' => fake()->optional(0.3)->dateTimeBetween('+1 month', '+1 year'),
            'uploaded_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}


