<?php

use App\Models\Core\Order\Order;
use App\Models\Core\Order\OrderItem;
use App\Models\Core\Order\SupplierOrder;
use App\Models\Core\Entity;
use App\Models\Core\Article;
use App\Models\Core\Proposal\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pode criar uma order', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    $order = Order::create([
        'number' => Order::nextNumber(),
        'order_date' => now(),
        'client_id' => $client->id,
        'delivery_date' => now()->addDays(15),
        'total_amount' => 1000,
        'status' => 'draft',
    ]);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->client_id)->toBe($client->id)
        ->and($order->status)->toBe('draft');
});

test('pode calcular total a partir de items', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $article1 = Article::factory()->create(['price' => 150]);
    $article2 = Article::factory()->create(['price' => 200]);

    $order = Order::create([
        'number' => Order::nextNumber(),
        'order_date' => now(),
        'client_id' => $client->id,
        'delivery_date' => now()->addDays(15),
        'total_amount' => 0,
        'status' => 'draft',
    ]);

    // Criar items
    OrderItem::create([
        'order_id' => $order->id,
        'article_id' => $article1->id,
        'quantity' => 3,
        'unit_price' => 150,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'article_id' => $article2->id,
        'quantity' => 2,
        'unit_price' => 200,
    ]);

    // Calcular total
    $total = $order->fresh()->calculateTotal();

    // (3 * 150) + (2 * 200) = 450 + 400 = 850
    expect($total)->toEqual(850.0)
        ->and($order->fresh()->total_amount)->toEqual(850.0);
});

test('order pertence a um cliente', function () {
    $client = Entity::factory()->create(['types' => ['client'], 'name' => 'Cliente ABC']);

    $order = Order::factory()->create(['client_id' => $client->id]);

    expect($order->client)->toBeInstanceOf(Entity::class)
        ->and($order->client->name)->toBe('Cliente ABC');
});

test('order pode ter proposal associada', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $proposal = Proposal::factory()->create(['client_id' => $client->id]);

    $order = Order::factory()->create([
        'client_id' => $client->id,
        'proposal_id' => $proposal->id,
    ]);

    expect($order->proposal)->toBeInstanceOf(Proposal::class)
        ->and($order->proposal->id)->toBe($proposal->id);
});

test('order pode ter multiplos items', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $article1 = Article::factory()->create();
    $article2 = Article::factory()->create();
    $article3 = Article::factory()->create();

    $order = Order::create([
        'number' => Order::nextNumber(),
        'order_date' => now(),
        'client_id' => $client->id,
        'delivery_date' => now()->addDays(15),
        'total_amount' => 0,
        'status' => 'draft',
    ]);

    OrderItem::create(['order_id' => $order->id, 'article_id' => $article1->id, 'quantity' => 1, 'unit_price' => 100]);
    OrderItem::create(['order_id' => $order->id, 'article_id' => $article2->id, 'quantity' => 2, 'unit_price' => 150]);
    OrderItem::create(['order_id' => $order->id, 'article_id' => $article3->id, 'quantity' => 3, 'unit_price' => 200]);

    expect($order->items()->count())->toBe(3);
});

test('order pode converter para supplier orders', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $supplier1 = Entity::factory()->create(['types' => ['supplier'], 'name' => 'Fornecedor A']);
    $supplier2 = Entity::factory()->create(['types' => ['supplier'], 'name' => 'Fornecedor B']);
    $article1 = Article::factory()->create(['price' => 100]);
    $article2 = Article::factory()->create(['price' => 200]);
    $article3 = Article::factory()->create(['price' => 150]);

    $order = Order::create([
        'number' => Order::nextNumber(),
        'order_date' => now(),
        'client_id' => $client->id,
        'delivery_date' => now()->addDays(15),
        'total_amount' => 1000,
        'status' => 'draft',
    ]);

    // Criar items com diferentes fornecedores
    OrderItem::create([
        'order_id' => $order->id,
        'article_id' => $article1->id,
        'supplier_id' => $supplier1->id,
        'quantity' => 2,
        'unit_price' => 100,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'article_id' => $article2->id,
        'supplier_id' => $supplier1->id,
        'quantity' => 1,
        'unit_price' => 200,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'article_id' => $article3->id,
        'supplier_id' => $supplier2->id,
        'quantity' => 3,
        'unit_price' => 150,
    ]);

    // Converter para supplier orders
    $supplierOrders = $order->convertToSupplierOrders();

    // Deve criar 2 supplier orders (um para cada fornecedor)
    expect($supplierOrders)->toHaveCount(2)
        ->and($order->supplierOrders()->count())->toBe(2);

    // Verificar totais
    $supplierOrder1 = SupplierOrder::where('supplier_id', $supplier1->id)->first();
    $supplierOrder2 = SupplierOrder::where('supplier_id', $supplier2->id)->first();

    // Fornecedor 1: (2 * 100) + (1 * 200) = 400
    expect($supplierOrder1->total_amount)->toEqual(400.0);

    // Fornecedor 2: (3 * 150) = 450
    expect($supplierOrder2->total_amount)->toEqual(450.0);
});

test('scope draft retorna apenas orders em rascunho', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    Order::factory()->create(['client_id' => $client->id, 'status' => 'draft']);
    Order::factory()->create(['client_id' => $client->id, 'status' => 'closed']);
    Order::factory()->create(['client_id' => $client->id, 'status' => 'draft']);

    expect(Order::draft()->count())->toBe(2);
});

test('scope closed retorna apenas orders fechadas', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    Order::factory()->create(['client_id' => $client->id, 'status' => 'draft']);
    Order::factory()->create(['client_id' => $client->id, 'status' => 'closed']);
    Order::factory()->create(['client_id' => $client->id, 'status' => 'closed']);

    expect(Order::closed()->count())->toBe(2);
});

test('gera numero sequencial correto', function () {
    $number = Order::nextNumber();
    expect($number)->toMatch('/^\d{6}$/');

    $order = Order::factory()->create();
    expect($order->number)->not->toBeNull();
});

test('datas sao salvas corretamente', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    $order = Order::create([
        'number' => Order::nextNumber(),
        'order_date' => '2025-10-13',
        'client_id' => $client->id,
        'delivery_date' => '2025-10-28',
        'total_amount' => 500,
        'status' => 'draft',
    ]);

    $retrieved = Order::find($order->id);

    expect($retrieved->order_date->toDateString())->toBe('2025-10-13')
        ->and($retrieved->delivery_date->toDateString())->toBe('2025-10-28');
});

test('scope filter funciona com status', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    Order::factory()->create(['client_id' => $client->id, 'status' => 'draft']);
    Order::factory()->create(['client_id' => $client->id, 'status' => 'closed']);
    Order::factory()->create(['client_id' => $client->id, 'status' => 'draft']);

    $drafts = Order::filter(['status' => 'draft'])->get();

    expect($drafts)->toHaveCount(2);
});

test('scope filter funciona com cliente', function () {
    $client1 = Entity::factory()->create(['types' => ['client']]);
    $client2 = Entity::factory()->create(['types' => ['client']]);

    Order::factory()->create(['client_id' => $client1->id]);
    Order::factory()->create(['client_id' => $client2->id]);
    Order::factory()->create(['client_id' => $client1->id]);

    $results = Order::filter(['client_id' => $client1->id])->get();

    expect($results)->toHaveCount(2);
});

test('order com soft delete pode ser restaurada', function () {
    $order = Order::factory()->create();
    $orderId = $order->id;

    // Soft delete
    $order->delete();

    // Verificar que foi deletada
    expect(Order::find($orderId))->toBeNull();
    expect(Order::withTrashed()->find($orderId))->not->toBeNull();

    // Restaurar
    $order->restore();

    expect(Order::find($orderId))->not->toBeNull();
});

