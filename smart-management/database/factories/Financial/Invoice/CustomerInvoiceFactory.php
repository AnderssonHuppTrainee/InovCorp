<?php

namespace Database\Factories\Financial\Invoice;

use App\Models\Financial\Invoice\CustomerInvoice;
use App\Models\Core\Entity;
use App\Models\Core\Order\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Financial\Invoice\CustomerInvoice>
 */
class CustomerInvoiceFactory extends Factory
{
    protected $model = CustomerInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoiceDate = fake()->dateTimeBetween('-3 months', 'now');
        $dueDate = (clone $invoiceDate)->modify('+30 days');
        $totalAmount = fake()->randomFloat(2, 100, 10000);
        $paidAmount = fake()->randomFloat(2, 0, $totalAmount);

        // Criar customer se não existir nenhum
        $customer = Entity::clients()->inRandomOrder()->first() 
            ?? Entity::factory()->create(['types' => ['client']]);

        // Order é opcional
        $order = Order::inRandomOrder()->first();

        return [
            'number' => CustomerInvoice::nextNumber(),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'customer_id' => $customer->id,
            'order_id' => $order?->id,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance' => $totalAmount - $paidAmount,
            'status' => fake()->randomElement(['draft', 'sent', 'partially_paid', 'paid', 'overdue']),
        ];
    }
}

