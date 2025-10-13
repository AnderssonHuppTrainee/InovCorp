<?php

namespace Database\Factories\Financial\Invoice;

use App\Models\Financial\Invoice\SupplierInvoice;
use App\Models\Core\Entity;
use App\Models\Core\Order\SupplierOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Financial\Invoice\SupplierInvoice>
 */
class SupplierInvoiceFactory extends Factory
{
    protected $model = SupplierInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoiceDate = fake()->dateTimeBetween('-3 months', 'now');
        $dueDate = (clone $invoiceDate)->modify('+30 days');

        // Criar supplier se não existir nenhum
        $supplier = Entity::suppliers()->inRandomOrder()->first()
            ?? Entity::factory()->create(['types' => ['supplier']]);

        // Criar supplier order se não existir nenhuma (opcional)
        $supplierOrder = SupplierOrder::inRandomOrder()->first();

        return [
            'number' => SupplierInvoice::nextNumber(),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'supplier_id' => $supplier->id,
            'supplier_order_id' => $supplierOrder?->id,
            'total_amount' => fake()->randomFloat(2, 100, 10000),
            'status' => fake()->randomElement(['pending_payment', 'paid']),
        ];
    }
}

