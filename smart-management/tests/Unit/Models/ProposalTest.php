<?php

use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Proposal\ProposalItem;
use App\Models\Core\Entity;
use App\Models\Core\Article;
use App\Models\Core\Order\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pode criar uma proposta', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    $proposal = Proposal::create([
        'number' => Proposal::nextNumber(),
        'proposal_date' => now(),
        'client_id' => $client->id,
        'validity_date' => now()->addDays(30),
        'total_amount' => 1000,
        'status' => 'draft',
    ]);

    expect($proposal)->toBeInstanceOf(Proposal::class)
        ->and($proposal->client_id)->toBe($client->id)
        ->and($proposal->status)->toBe('draft');
});

test('pode calcular total de items', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $article1 = Article::factory()->create(['price' => 100]);
    $article2 = Article::factory()->create(['price' => 50]);

    $proposal = Proposal::create([
        'number' => Proposal::nextNumber(),
        'proposal_date' => now(),
        'client_id' => $client->id,
        'validity_date' => now()->addDays(30),
        'total_amount' => 0,
        'status' => 'draft',
    ]);

    // criar itens
    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article1->id,
        'quantity' => 2,
        'unit_price' => 100,
    ]);

    ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article2->id,
        'quantity' => 3,
        'unit_price' => 50,
    ]);


    $total = $proposal->fresh()->calculateTotal();

    expect($total)->toEqual(350.0)
        ->and($proposal->fresh()->total_amount)->toEqual(350.0);
});

test('pode converter uma proposta em order', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $supplier = Entity::factory()->create(['types' => ['supplier']]);
    $article = Article::factory()->create(['price' => 100]);

    $proposal = Proposal::create([
        'number' => Proposal::nextNumber(),
        'proposal_date' => now(),
        'client_id' => $client->id,
        'validity_date' => now()->addDays(30),
        'total_amount' => 500,
        'status' => 'draft',
    ]);


    $proposalItem = ProposalItem::create([
        'proposal_id' => $proposal->id,
        'article_id' => $article->id,
        'supplier_id' => $supplier->id,
        'quantity' => 5,
        'unit_price' => 100,
    ]);


    $order = $proposal->convertToOrder();

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->client_id)->toBe($client->id)
        ->and($order->proposal_id)->toBe($proposal->id)
        ->and($order->items()->count())->toBe(1);

    $orderItem = $order->items()->first();
    expect($orderItem->supplier_id)->toBe($supplier->id)
        ->and($orderItem->article_id)->toBe($article->id)
        ->and($orderItem->quantity)->toBe(5)
        ->and($orderItem->unit_price)->toEqual(100.0);

    expect($proposal->fresh()->status)->toBe('closed');
});

test('pode converter propostas com multiplos items preservando todos supplier_ids', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $supplier1 = Entity::factory()->create(['types' => ['supplier']]);
    $supplier2 = Entity::factory()->create(['types' => ['supplier']]);
    $article1 = Article::factory()->create(['price' => 200]);
    $article2 = Article::factory()->create(['price' => 150]);

    $proposal = Proposal::create([
        'number' => Proposal::nextNumber(),
        'proposal_date' => now(),
        'client_id' => $client->id,
        'validity_date' => now()->addDays(30),
        'total_amount' => 1000,
        'status' => 'draft',
    ]);


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


    $order = $proposal->convertToOrder();

    expect($order->items()->count())->toBe(2);


    $orderItems = $order->items()->get();

    expect($orderItems[0]->supplier_id)->toBe($supplier1->id)
        ->and($orderItems[1]->supplier_id)->toBe($supplier2->id);
});

test('pode gerar num sequencial correto', function () {

    $number = Proposal::nextNumber();
    expect($number)->toMatch('/^\d{6}$/');

    $proposal = Proposal::factory()->create();
    expect($proposal->number)->not->toBeNull();
});

test('pode filtrar propostas por status', function () {
    Proposal::factory()->create(['status' => 'draft']);
    Proposal::factory()->create(['status' => 'closed']);
    Proposal::factory()->create(['status' => 'draft']);

    $drafts = Proposal::draft()->get();
    $closed = Proposal::closed()->get();

    expect($drafts)->toHaveCount(2)
        ->and($closed)->toHaveCount(1);
});

