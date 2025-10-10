<?php

namespace Database\Factories\System\Calendar;

use App\Models\System\Calendar\CalendarEvent;
use App\Models\System\Calendar\CalendarEventType;
use App\Models\System\Calendar\CalendarAction;
use App\Models\Core\Entity;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\System\Calendar\CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    protected $model = CalendarEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventDate = fake()->dateTimeBetween('-1 month', '+2 months');

        // Ocasionalmente compartilhar com outros usuários (30% de chance)
        $sharedWith = [];
        if (fake()->boolean(30)) {
            $randomUsers = User::inRandomOrder()->take(fake()->numberBetween(1, 3))->pluck('id')->toArray();
            $sharedWith = $randomUsers;
        }

        return [
            'event_date' => $eventDate->format('Y-m-d'),
            'event_time' => fake()->time('H:i'),
            'duration' => fake()->randomElement([30, 60, 90, 120, 180]),
            'shared_with' => $sharedWith, // laravel fará o JSON encode automaticamente
            'knowledge' => fake()->boolean(30),
            'entity_id' => Entity::inRandomOrder()->first()?->id,
            'calendar_event_type_id' => CalendarEventType::inRandomOrder()->first()?->id,
            'calendar_action_id' => CalendarAction::inRandomOrder()->first()?->id,
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled']),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}

