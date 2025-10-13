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
        // Criar supplier se não existir nenhum
        $supplier = Entity::suppliers()->inRandomOrder()->first() 
            ?? Entity::factory()->create(['types' => ['supplier']]);

        // Criar order se não existir nenhuma
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        return [
            'number' => SupplierOrder::nextNumber(),
            'order_date' => fake()->dateTimeBetween('-2 months', '+1 month'),
            'supplier_id' => $supplier->id,
            'order_id' => $order->id,
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'status' => fake()->randomElement(['draft', 'closed']),
        ];
    }
}

