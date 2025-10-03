<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_by_status_priority_and_due_date(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Task::factory()->create(['user_id' => $user->id, 'title' => 'A', 'status' => 'pending', 'priority' => 'low', 'due_date' => now()->addDays(5)->toDateString()]);
        Task::factory()->create(['user_id' => $user->id, 'title' => 'B', 'status' => 'completed', 'priority' => 'high', 'due_date' => now()->addDays(1)->toDateString()]);

        $response = $this->get(route('tasks.index', [
            'status' => 'completed',
            'priority' => 'high',
            'due_from' => now()->toDateString(),
            'due_to' => now()->addDays(2)->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('tasks/Index')
                ->has('tasks.data', 1)
                ->where('tasks.data.0.title', 'B')
        );
    }
}




