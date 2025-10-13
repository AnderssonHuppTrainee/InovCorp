<?php

namespace Database\Factories\Core;

use App\Models\Core\WorkOrder;
use App\Models\Core\Entity;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+' . rand(1, 30) . ' days');

        // Criar client se não existir nenhum
        $client = Entity::clients()->inRandomOrder()->first()
            ?? Entity::factory()->create(['types' => ['client']]);

        // Criar user se não existir nenhum
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'number' => WorkOrder::nextNumber(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'client_id' => $client->id,
            'assigned_to' => $user->id,
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
        ];
    }
}



