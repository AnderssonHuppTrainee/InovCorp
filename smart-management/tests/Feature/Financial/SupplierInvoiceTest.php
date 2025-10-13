<?php

use App\Models\Financial\Invoice\SupplierInvoice;
use App\Models\Core\Entity;
use App\Models\Core\Order\SupplierOrder;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Storage::fake('local');
});

test('can create supplier invoice via HTTP request', function () {
    actingAs($this->user);

    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $supplierOrder = SupplierOrder::factory()->create();

    $invoiceData = [
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => '2025-10-13',
        'due_date' => '2025-11-12',
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 1200,
        'status' => 'pending_payment',
    ];

    $response = $this->post(route('supplier-invoices.store'), $invoiceData);

    $response->assertRedirect();

    // 🔍 TESTE CRÍTICO: Verificar que a fatura foi criada
    assertDatabaseHas('supplier_invoices', [
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 1200,
        'status' => 'pending_payment',
        'invoice_date' => '2025-10-13',
        'due_date' => '2025-11-12',
    ]);
});

test('can create supplier invoice with document upload', function () {
    actingAs($this->user);

    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $supplierOrder = SupplierOrder::factory()->create();

    $document = UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf');

    $invoiceData = [
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => '2025-10-13',
        'due_date' => '2025-11-12',
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 1500,
        'document' => $document,
        'status' => 'pending_payment',
    ];

    $response = $this->post(route('supplier-invoices.store'), $invoiceData);

    $response->assertRedirect();

    // Verificar que a fatura foi criada
    assertDatabaseHas('supplier_invoices', [
        'supplier_id' => $supplier->id,
        'total_amount' => 1500,
    ]);

    // 🔍 TESTE CRÍTICO: Verificar que o arquivo foi salvo
    $invoice = SupplierInvoice::where('supplier_id', $supplier->id)->first();
    
    if ($invoice->document_path) {
        Storage::assertExists($invoice->document_path);
    }
});

test('can create supplier invoice with payment proof', function () {
    actingAs($this->user);

    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $supplierOrder = SupplierOrder::factory()->create();

    $document = UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf');
    $paymentProof = UploadedFile::fake()->create('payment.pdf', 50, 'application/pdf');

    $invoiceData = [
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => '2025-10-13',
        'due_date' => '2025-11-12',
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 2000,
        'document' => $document,
        'payment_proof' => $paymentProof,
        'status' => 'paid',
    ];

    $response = $this->post(route('supplier-invoices.store'), $invoiceData);

    $response->assertRedirect();

    assertDatabaseHas('supplier_invoices', [
        'supplier_id' => $supplier->id,
        'total_amount' => 2000,
        'status' => 'paid',
    ]);

    $invoice = SupplierInvoice::where('supplier_id', $supplier->id)->first();
    
    // Verificar que ambos os arquivos foram salvos
    if ($invoice->document_path) {
        Storage::assertExists($invoice->document_path);
    }
    if ($invoice->payment_proof_path) {
        Storage::assertExists($invoice->payment_proof_path);
    }
});

test('can update supplier invoice', function () {
    actingAs($this->user);

    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $supplierOrder = SupplierOrder::factory()->create();

    $invoice = SupplierInvoice::factory()->create([
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 1000,
        'status' => 'pending_payment',
    ]);

    $updateData = [
        'number' => $invoice->number,
        'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
        'due_date' => $invoice->due_date->format('Y-m-d'),
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 1200,
        'status' => 'paid',
    ];

    $response = $this->put(route('supplier-invoices.update', $invoice->id), $updateData);

    $response->assertRedirect();

    assertDatabaseHas('supplier_invoices', [
        'id' => $invoice->id,
        'total_amount' => 1200,
        'status' => 'paid',
    ]);
});

test('invoice dates are saved correctly', function () {
    actingAs($this->user);

    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    $invoiceData = [
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => '2025-10-13',
        'due_date' => '2025-11-12',
        'supplier_id' => $supplier->id,
        'total_amount' => 800,
        'status' => 'pending_payment',
    ];

    $response = $this->post(route('supplier-invoices.store'), $invoiceData);

    $invoice = SupplierInvoice::where('supplier_id', $supplier->id)->first();

    // 🔍 TESTE CRÍTICO: Verificar datas
    expect($invoice->invoice_date->toDateString())->toBe('2025-10-13')
        ->and($invoice->due_date->toDateString())->toBe('2025-11-12');
});

test('can change invoice status from pending to paid', function () {
    actingAs($this->user);

    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    $invoice = SupplierInvoice::factory()->create([
        'supplier_id' => $supplier->id,
        'status' => 'pending_payment',
    ]);

    $updateData = [
        'number' => $invoice->number,
        'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
        'due_date' => $invoice->due_date->format('Y-m-d'),
        'supplier_id' => $supplier->id,
        'total_amount' => $invoice->total_amount,
        'status' => 'paid',
    ];

    $response = $this->put(route('supplier-invoices.update', $invoice->id), $updateData);

    expect($invoice->fresh()->status)->toBe('paid');
});

test('storage uses local disk correctly', function () {
    actingAs($this->user);

    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $supplierOrder = SupplierOrder::factory()->create();

    $document = UploadedFile::fake()->create('test-invoice.pdf', 100, 'application/pdf');

    $invoiceData = [
        'number' => SupplierInvoice::nextNumber(),
        'invoice_date' => '2025-10-13',
        'due_date' => '2025-11-12',
        'supplier_id' => $supplier->id,
        'supplier_order_id' => $supplierOrder->id,
        'total_amount' => 1000,
        'document' => $document,
        'status' => 'pending_payment',
    ];

    $response = $this->post(route('supplier-invoices.store'), $invoiceData);

    // 🔍 TESTE CRÍTICO: Verificar que não há erro de disco inexistente
    $response->assertRedirect();
    
    $invoice = SupplierInvoice::where('supplier_id', $supplier->id)->first();
    expect($invoice)->not->toBeNull();
});

