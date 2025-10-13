<?php

use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Proposal\ProposalItem;
use App\Models\Core\Entity;
use App\Models\Core\Article;
use App\Models\Core\Order\Order;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('proposal converts to order via HTTP request preserving supplier_id', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);
    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $article = Article::factory()->create();
    
    $proposal = Proposal::factory()->create([
        'client_id' => $client->id,
        'status' => 'draft',
        'total_amount' => 500,
    ]);

    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article->id,
        'supplier_id' => $supplier->id,
        'quantity' => 5,
        'unit_price' => 100,
    ]);

    // Fazer POST request para converter
    $response = $this->post(route('proposals.convert-to-order', $proposal->id));

    $response->assertRedirect();

    // Verificar que a order foi criada
    assertDatabaseHas('orders', [
        'proposal_id' => $proposal->id,
        'client_id' => $client->id,
        'status' => 'draft',
    ]);

    // 🔍 TESTE CRÍTICO: Verificar que supplier_id foi preservado
    assertDatabaseHas('order_items', [
        'article_id' => $article->id,
        'supplier_id' => $supplier->id,
        'quantity' => 5,
        'unit_price' => 100,
    ]);

    // Verificar que proposta foi fechada
    expect($proposal->fresh()->status)->toBe('closed');
});

test('proposal with multiple items converts preserving all supplier data', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);
    $supplier1 = Entity::factory()->create(['types' => ['supplier'], 'name' => 'Supplier A']);
    $supplier2 = Entity::factory()->create(['types' => ['supplier'], 'name' => 'Supplier B']);
    $supplier3 = Entity::factory()->create(['types' => ['supplier'], 'name' => 'Supplier C']);
    
    $article1 = Article::factory()->create();
    $article2 = Article::factory()->create();
    $article3 = Article::factory()->create();
    
    $proposal = Proposal::factory()->create([
        'client_id' => $client->id,
        'status' => 'draft',
        'total_amount' => 1500,
    ]);

    // Criar 3 itens com fornecedores diferentes
    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article1->id,
        'supplier_id' => $supplier1->id,
        'quantity' => 2,
        'unit_price' => 200,
    ]);

    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article2->id,
        'supplier_id' => $supplier2->id,
        'quantity' => 3,
        'unit_price' => 150,
    ]);

    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article3->id,
        'supplier_id' => $supplier3->id,
        'quantity' => 1,
        'unit_price' => 500,
    ]);

    // Converter
    $response = $this->post(route('proposals.convert', $proposal->id));
    $response->assertRedirect();

    // Verificar todos os supplier_ids foram preservados
    assertDatabaseHas('order_items', [
        'article_id' => $article1->id,
        'supplier_id' => $supplier1->id,
    ]);

    assertDatabaseHas('order_items', [
        'article_id' => $article2->id,
        'supplier_id' => $supplier2->id,
    ]);

    assertDatabaseHas('order_items', [
        'article_id' => $article3->id,
        'supplier_id' => $supplier3->id,
    ]);

    // Verificar que a order tem 3 items
    $order = Order::where('proposal_id', $proposal->id)->first();
    expect($order->items()->count())->toBe(3);
});

test('cannot convert already converted proposal', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);
    $proposal = Proposal::factory()->create([
        'client_id' => $client->id,
        'status' => 'closed',
    ]);

    // Primeira conversão
    $order = $proposal->convertToOrder();

    // Tentar converter novamente (não deve criar nova order)
    $response = $this->post(route('proposals.convert', $proposal->id));
    
    // Deve haver apenas 1 order para esta proposal
    expect(Order::where('proposal_id', $proposal->id)->count())->toBe(1);
});

test('converted order has correct totals', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);
    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $article1 = Article::factory()->create();
    $article2 = Article::factory()->create();
    
    $proposal = Proposal::factory()->create([
        'client_id' => $client->id,
        'status' => 'draft',
        'total_amount' => 850,
    ]);

    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article1->id,
        'supplier_id' => $supplier->id,
        'quantity' => 2,
        'unit_price' => 100,
    ]);

    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article2->id,
        'supplier_id' => $supplier->id,
        'quantity' => 5,
        'unit_price' => 130,
    ]);

    $response = $this->post(route('proposals.convert', $proposal->id));

    $order = Order::where('proposal_id', $proposal->id)->first();
    
    // Total da order deve ser igual ao total da proposal
    expect($order->total_amount)->toBe($proposal->total_amount)
        ->and($order->total_amount)->toBe(850.0);
});

