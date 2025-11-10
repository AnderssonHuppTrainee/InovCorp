<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\System\Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countries = [
            ['name' => 'Portugal', 'code' => 'PT', 'phone_code' => '+351'],
            ['name' => 'Spain', 'code' => 'ES', 'phone_code' => '+34'],
            ['name' => 'France', 'code' => 'FR', 'phone_code' => '+33'],
            ['name' => 'Germany', 'code' => 'DE', 'phone_code' => '+49'],
            ['name' => 'Italy', 'code' => 'IT', 'phone_code' => '+39'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'phone_code' => '+44'],
            ['name' => 'Belgium', 'code' => 'BE', 'phone_code' => '+32'],
            ['name' => 'Netherlands', 'code' => 'NL', 'phone_code' => '+31'],
            ['name' => 'Switzerland', 'code' => 'CH', 'phone_code' => '+41'],
            ['name' => 'Poland', 'code' => 'PL', 'phone_code' => '+48'],
        ];

        $country = fake()->unique()->randomElement($countries);

        return [
            'name' => $country['name'],
            'code' => $country['code'],
            'phone_code' => $country['phone_code'],
            'is_active' => true,
        ];
    }
}

