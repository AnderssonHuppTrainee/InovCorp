<?php

namespace Database\Factories\Core\Order;

use App\Models\Core\Order\SupplierOrder;
use App\Models\Core\Entity;
use App\Models\Core\Order\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Order\SupplierOrder>
 */
class SupplierOrderFactory extends Factory
{
    protected $model = SupplierOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => SupplierOrder::nextNumber(),
            'order_date' => fake()->dateTimeBetween('-2 months', '+1 month'),
            'supplier_id' => Entity::suppliers()->inRandomOrder()->first()?->id,
            'order_id' => Order::inRandomOrder()->first()?->id,
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'status' => fake()->randomElement(['draft', 'closed']),
        ];
    }
}

