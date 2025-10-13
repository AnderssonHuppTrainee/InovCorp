<?php

use App\Models\Financial\Invoice\CustomerInvoice;
use App\Models\Core\Entity;
use App\Models\Core\Order\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pode criar uma customer invoice', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);
    $order = Order::factory()->create(['client_id' => $customer->id]);

    $invoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'customer_id' => $customer->id,
        'order_id' => $order->id,
        'total_amount' => 1500,
        'paid_amount' => 0,
        'balance' => 1500,
        'status' => 'draft',
    ]);

    expect($invoice)->toBeInstanceOf(CustomerInvoice::class)
        ->and($invoice->customer_id)->toBe($customer->id)
        ->and($invoice->order_id)->toBe($order->id)
        ->and($invoice->total_amount)->toEqual(1500.0)
        ->and($invoice->balance)->toEqual(1500.0);
});

test('datas da invoice sao salvas corretamente', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    $invoiceDate = '2025-10-13';
    $dueDate = '2025-11-12';

    $invoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => $invoiceDate,
        'due_date' => $dueDate,
        'customer_id' => $customer->id,
        'total_amount' => 2000,
        'paid_amount' => 0,
        'balance' => 2000,
        'status' => 'sent',
    ]);

    $retrieved = CustomerInvoice::find($invoice->id);

    expect($retrieved->invoice_date->toDateString())->toBe($invoiceDate)
        ->and($retrieved->due_date->toDateString())->toBe($dueDate);
});

test('pode registrar pagamento parcial', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    $invoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 1000,
        'paid_amount' => 0,
        'balance' => 1000,
        'status' => 'sent',
    ]);

    // Registrar pagamento parcial de 400
    $invoice->registerPayment(400);

    expect($invoice->paid_amount)->toEqual(400.0)
        ->and($invoice->balance)->toEqual(600.0)
        ->and($invoice->status)->toBe('partially_paid');
});

test('pode registrar pagamento total', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    $invoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 1000,
        'paid_amount' => 0,
        'balance' => 1000,
        'status' => 'sent',
    ]);

    // Registrar pagamento total de 1000
    $invoice->registerPayment(1000);

    expect($invoice->paid_amount)->toEqual(1000.0)
        ->and($invoice->balance)->toEqual(0.0)
        ->and($invoice->status)->toBe('paid');
});

test('pode registrar multiplos pagamentos', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    $invoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 1500,
        'paid_amount' => 0,
        'balance' => 1500,
        'status' => 'sent',
    ]);

    // Primeiro pagamento
    $invoice->registerPayment(500);
    expect($invoice->status)->toBe('partially_paid')
        ->and($invoice->balance)->toEqual(1000.0);

    // Segundo pagamento
    $invoice->registerPayment(500);
    expect($invoice->status)->toBe('partially_paid')
        ->and($invoice->balance)->toEqual(500.0);

    // Pagamento final
    $invoice->registerPayment(500);
    expect($invoice->status)->toBe('paid')
        ->and($invoice->balance)->toEqual(0.0);
});

test('pode detectar invoices atrasadas', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    // Invoice vencida
    $overdueInvoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now()->subDays(60),
        'due_date' => now()->subDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 500,
        'paid_amount' => 0,
        'balance' => 500,
        'status' => 'sent',
    ]);

    // Invoice não vencida
    $currentInvoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 800,
        'paid_amount' => 0,
        'balance' => 800,
        'status' => 'sent',
    ]);

    // Invoice paga (não deve ser considerada vencida)
    $paidInvoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now()->subDays(60),
        'due_date' => now()->subDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 300,
        'paid_amount' => 300,
        'balance' => 0,
        'status' => 'paid',
    ]);

    expect($overdueInvoice->isOverdue())->toBeTrue()
        ->and($currentInvoice->isOverdue())->toBeFalse()
        ->and($paidInvoice->isOverdue())->toBeFalse();
});

test('scopes de status funcionam', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    CustomerInvoice::factory()->create(['customer_id' => $customer->id, 'status' => 'draft']);
    CustomerInvoice::factory()->create(['customer_id' => $customer->id, 'status' => 'sent']);
    CustomerInvoice::factory()->create(['customer_id' => $customer->id, 'status' => 'paid']);
    CustomerInvoice::factory()->create(['customer_id' => $customer->id, 'status' => 'partially_paid']);

    expect(CustomerInvoice::draft()->count())->toBe(1)
        ->and(CustomerInvoice::sent()->count())->toBe(1)
        ->and(CustomerInvoice::paid()->count())->toBe(1)
        ->and(CustomerInvoice::partiallyPaid()->count())->toBe(1);
});

test('scope overdue detecta invoices vencidas', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    // Invoice overdue
    CustomerInvoice::factory()->create([
        'customer_id' => $customer->id,
        'due_date' => now()->subDays(10),
        'status' => 'sent',
    ]);

    // Invoice partially paid e overdue
    CustomerInvoice::factory()->create([
        'customer_id' => $customer->id,
        'due_date' => now()->subDays(5),
        'status' => 'partially_paid',
    ]);

    // Invoice paid (não deve aparecer)
    CustomerInvoice::factory()->create([
        'customer_id' => $customer->id,
        'due_date' => now()->subDays(30),
        'status' => 'paid',
    ]);

    expect(CustomerInvoice::overdue()->count())->toBeGreaterThanOrEqual(2);
});

test('invoice pertence a customer e order', function () {
    $customer = Entity::factory()->create(['types' => ['client'], 'name' => 'Cliente ABC']);
    $order = Order::factory()->create(['client_id' => $customer->id]);

    $invoice = CustomerInvoice::factory()->create([
        'customer_id' => $customer->id,
        'order_id' => $order->id,
    ]);

    expect($invoice->customer)->toBeInstanceOf(Entity::class)
        ->and($invoice->customer->name)->toBe('Cliente ABC')
        ->and($invoice->order)->toBeInstanceOf(Order::class)
        ->and($invoice->order->id)->toBe($order->id);
});

test('updateStatus atualiza status corretamente', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    $invoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 1000,
        'paid_amount' => 0,
        'balance' => 1000,
        'status' => 'sent',
    ]);

    // Fazer pagamento
    $invoice->paid_amount = 1000;
    $invoice->balance = 0;
    $invoice->updateStatus();

    expect($invoice->status)->toBe('paid');
});

test('updateStatus detecta overdue', function () {
    $customer = Entity::factory()->create(['types' => ['client']]);

    $invoice = CustomerInvoice::create([
        'number' => CustomerInvoice::nextNumber(),
        'invoice_date' => now()->subDays(60),
        'due_date' => now()->subDays(30),
        'customer_id' => $customer->id,
        'total_amount' => 1000,
        'paid_amount' => 0,
        'balance' => 1000,
        'status' => 'sent',
    ]);

    $invoice->updateStatus();

    expect($invoice->status)->toBe('overdue');
});

test('gera numero sequencial correto', function () {
    $number = CustomerInvoice::nextNumber();
    expect($number)->toMatch('/^\d{6}$/');

    $invoice = CustomerInvoice::factory()->create();
    expect($invoice->number)->not->toBeNull();
});

test('scope filter funciona', function () {
    $customer1 = Entity::factory()->create(['types' => ['client']]);
    $customer2 = Entity::factory()->create(['types' => ['client']]);

    CustomerInvoice::factory()->create(['customer_id' => $customer1->id, 'status' => 'sent']);
    CustomerInvoice::factory()->create(['customer_id' => $customer2->id, 'status' => 'paid']);
    CustomerInvoice::factory()->create(['customer_id' => $customer1->id, 'status' => 'draft']);

    // Filtrar por customer
    $customer1Invoices = CustomerInvoice::filter(['customer_id' => $customer1->id])->get();
    expect($customer1Invoices)->toHaveCount(2);

    // Filtrar por status
    $sentInvoices = CustomerInvoice::filter(['status' => 'sent'])->get();
    expect($sentInvoices)->toHaveCount(1);
});

test('invoice com soft delete pode ser restaurada', function () {
    $invoice = CustomerInvoice::factory()->create();
    $invoiceId = $invoice->id;

    // Soft delete
    $invoice->delete();

    // Verificar que foi deletada
    expect(CustomerInvoice::find($invoiceId))->toBeNull();
    expect(CustomerInvoice::withTrashed()->find($invoiceId))->not->toBeNull();

    // Restaurar
    $invoice->restore();

    expect(CustomerInvoice::find($invoiceId))->not->toBeNull();
});

