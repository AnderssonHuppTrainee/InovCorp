<?php

namespace Database\Factories\Core;

use App\Models\Core\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Catalog\ContactRole;
use App\Models\Core\Entity;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numerify('######'),
            'entity_id' => Entity::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'contact_role_id' => ContactRole::factory(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'gdpr_consent' => true
        ];
    }
}
