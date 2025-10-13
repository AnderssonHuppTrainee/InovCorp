<?php

namespace Database\Factories\Core\Order;

use App\Models\Core\Order\Order;
use App\Models\Core\Article;
use App\Models\Core\Entity;
use App\Models\Core\Proposal\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Order\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $deliveryDate = (clone $orderDate)->modify('+15 days');

        // Criar client se não existir nenhum
        $client = Entity::clients()->inRandomOrder()->first() 
            ?? Entity::factory()->create(['types' => ['client']]);

        // Proposal é opcional
        $proposal = Proposal::inRandomOrder()->first();

        return [
            'number' => Order::nextNumber(),
            'order_date' => $orderDate,
            'client_id' => $client->id,
            'proposal_id' => $proposal?->id,
            'delivery_date' => $deliveryDate,
            'status' => fake()->randomElement(['draft', 'closed']),
            'total_amount' => 0, // Será calculado pelos items
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            // Criar 2-5 itens para a encomenda
            $itemCount = rand(2, 5);

            for ($i = 0; $i < $itemCount; $i++) {
                $article = Article::where('status', 'active')->inRandomOrder()->first();
                $supplier = Entity::suppliers()->inRandomOrder()->first();

                if ($article) {
                    $quantity = rand(1, 10);
                    $unitPrice = $article->price;

                    $order->items()->create([
                        'article_id' => $article->id,
                        'supplier_id' => $supplier?->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'notes' => fake()->optional(0.3)->sentence(),
                    ]);
                }
            }

            // Recalcular total
            $order->calculateTotal();
        });
    }
}



