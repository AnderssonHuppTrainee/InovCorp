<?php

use App\Models\Financial\Invoice\SupplierInvoice;
use App\Models\Core\Entity;
use App\Models\Core\Order\SupplierOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pode criar uma fatura fornecedor', function () {
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

test('datas da fatura são salvas corretamente', function () {
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


    $retrieved = SupplierInvoice::find($invoice->id);
    expect($retrieved->invoice_date->toDateString())->toBe($invoiceDate)
        ->and($retrieved->due_date->toDateString())->toBe($dueDate);
});

test('pode detetar faturas atrasadas', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);


    $overdueInvoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => now()->subDays(60),
        'due_date' => now()->subDays(30),
        'supplier_id' => $supplier->id,
        'total_amount' => 500,
        'status' => 'pending_payment',
    ]);


    $currentInvoice = SupplierInvoice::create([
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'supplier_id' => $supplier->id,
        'total_amount' => 800,
        'status' => 'pending_payment',
    ]);


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

test('pode filtrar por status', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    SupplierInvoice::factory()->create(['supplier_id' => $supplier->id, 'status' => 'pending_payment']);
    SupplierInvoice::factory()->create(['supplier_id' => $supplier->id, 'status' => 'paid']);
    SupplierInvoice::factory()->create(['supplier_id' => $supplier->id, 'status' => 'pending_payment']);

    expect(SupplierInvoice::pendingPayment()->count())->toBe(2)
        ->and(SupplierInvoice::paid()->count())->toBe(1);
});

test('gera num sequencial correto', function () {

    $number = SupplierInvoice::nextNumber();
    expect($number)->toMatch('/^\d{6}$/');


    $invoice = SupplierInvoice::factory()->create();
    expect($invoice->number)->not->toBeNull();
});

test('pertence ao fornecedor e a uma order', function () {
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

test('pode atualizar o status', function () {
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

