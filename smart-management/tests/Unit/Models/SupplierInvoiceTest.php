<?php

use App\Models\Financial\Invoice\SupplierInvoice;
use App\Models\Core\Entity;
use App\Models\Core\Order\SupplierOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create a supplier invoice', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $supplierOrder = SupplierOrder::factory()->create();

    $invoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 1000,
        'status' => 'pending_payment',
    ]);

    expect($invoice)->toBeInstanceOf(SupplierInvoice::class)
        ->and($invoice->supplier_id)->toBe($supplier->id)
        ->and($invoice->status)->toBe('pending_payment')
        ->and($invoice->total_amount)->toEqual(1000.0);
});

test('invoice dates are persisted correctly', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    $invoiceDate = '2025-10-13';
    $dueDate = '2025-11-12';

    $invoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => $invoiceDate,
        'due_date' => $dueDate,
        'supplier_id' => $supplier->id,
        'total_amount' => 1200,
        'status' => 'pending_payment',
    ]);

    // 🔍 TESTE CRÍTICO: Verificar que datas são salvas
    $retrieved = SupplierInvoice::find($invoice->id);
    expect($retrieved->invoice_date->toDateString())->toBe($invoiceDate)
        ->and($retrieved->due_date->toDateString())->toBe($dueDate);
});

test('can detect overdue invoices', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    // Fatura vencida
    $overdueInvoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => now()->subDays(60),
        'due_date' => now()->subDays(30),
        'supplier_id' => $supplier->id,
        'total_amount' => 500,
        'status' => 'pending_payment',
    ]);

    // Fatura não vencida
    $currentInvoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'supplier_id' => $supplier->id,
        'total_amount' => 800,
        'status' => 'pending_payment',
    ]);

    // Fatura paga (não deve ser considerada vencida)
    $paidInvoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => now()->subDays(60),
        'due_date' => now()->subDays(30),
        'supplier_id' => $supplier->id,
        'total_amount' => 300,
        'status' => 'paid',
    ]);

    expect($overdueInvoice->isOverdue())->toBeTrue()
        ->and($currentInvoice->isOverdue())->toBeFalse()
        ->and($paidInvoice->isOverdue())->toBeFalse();

    expect(SupplierInvoice::overdue()->count())->toBe(1);
});

test('can filter invoices by status', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    SupplierInvoice::factory()->create(['supplier_id' => $supplier->id, 'status' => 'pending_payment']);
    SupplierInvoice::factory()->create(['supplier_id' => $supplier->id, 'status' => 'paid']);
    SupplierInvoice::factory()->create(['supplier_id' => $supplier->id, 'status' => 'pending_payment']);

    expect(SupplierInvoice::pendingPayment()->count())->toBe(2)
        ->and(SupplierInvoice::paid()->count())->toBe(1);
});

test('generates next number correctly', function () {
    // Verificar que nextNumber retorna formato válido (6 dígitos)
    $number = SupplierInvoice::nextNumber();
    expect($number)->toMatch('/^\d{6}$/');

    // Criar invoice e verificar que tem número válido
    $invoice = SupplierInvoice::factory()->create();
    expect($invoice->number)->not->toBeNull();
});

test('belongs to supplier and supplier order', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $supplierOrder = SupplierOrder::factory()->create();

    $invoice = SupplierInvoice::factory()->create([
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
    ]);

    expect($invoice->supplier)->toBeInstanceOf(Entity::class)
        ->and($invoice->supplier->id)->toBe($supplier->id)
        ->and($invoice->supplierOrder)->toBeInstanceOf(SupplierOrder::class)
        ->and($invoice->supplierOrder->id)->toBe($supplierOrder->id);
});

test('can update invoice status', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    $invoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'supplier_id' => $supplier->id,
        'total_amount' => 1000,
        'status' => 'pending_payment',
    ]);

    expect($invoice->status)->toBe('pending_payment');

    $invoice->update(['status' => 'paid']);

    expect($invoice->fresh()->status)->toBe('paid');
});

