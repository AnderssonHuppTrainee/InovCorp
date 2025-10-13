<?php

use App\Models\Core\WorkOrder;
use App\Models\Core\Entity;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('can create work order with dates via HTTP request', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);

    $workOrderData = [
        'number' => WorkOrder::nextNumber(),
        'title' => 'Instalação de Sistema',
        'description' => 'Instalar novo sistema de gestão',
        'client_id' => $client->id,
        'assigned_to' => $this->user->id,
        'priority' => 'high',
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
        'status' => 'pending',
    ];

    $response = $this->post(route('work-orders.store'), $workOrderData);

    $response->assertRedirect();

    // 🔍 TESTE CRÍTICO: Verificar que as datas foram salvas
    assertDatabaseHas('work_orders', [
        'title' => 'Instalação de Sistema',
        'client_id' => $client->id,
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
    ]);

    $workOrder = WorkOrder::where('title', 'Instalação de Sistema')->first();
    expect($workOrder->start_date->toDateString())->toBe('2025-10-13')
        ->and($workOrder->end_date->toDateString())->toBe('2025-10-20');
});

test('can update work order dates via HTTP request', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);

    $workOrder = WorkOrder::factory()->create([
        'client_id' => $client->id,
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
    ]);

    $updateData = [
        'number' => $workOrder->number,
        'title' => $workOrder->title,
        'description' => $workOrder->description,
        'client_id' => $client->id,
        'priority' => $workOrder->priority,
        'start_date' => '2025-10-15',
        'end_date' => '2025-10-25',
        'status' => $workOrder->status,
    ];

    $response = $this->put(route('work-orders.update', $workOrder->id), $updateData);

    $response->assertRedirect();

    // 🔍 TESTE CRÍTICO: Verificar que as datas foram atualizadas
    assertDatabaseHas('work_orders', [
        'id' => $workOrder->id,
        'start_date' => '2025-10-15',
        'end_date' => '2025-10-25',
    ]);

    $updated = WorkOrder::find($workOrder->id);
    expect($updated->start_date->toDateString())->toBe('2025-10-15')
        ->and($updated->end_date->toDateString())->toBe('2025-10-25');
});

test('dates persist across multiple operations', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);

    // Criar work order
    $workOrder = WorkOrder::create([
        'number' => WorkOrder::nextNumber(),
        'title' => 'Manutenção Preventiva',
        'description' => 'Realizar manutenção preventiva',
        'client_id' => $client->id,
        'priority' => 'medium',
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
        'status' => 'pending',
    ]);

    // Atualizar status
    $workOrder->update(['status' => 'in_progress']);

    // Buscar novamente do banco
    $retrieved = WorkOrder::find($workOrder->id);

    // Datas devem permanecer intactas
    expect($retrieved->start_date->toDateString())->toBe('2025-10-13')
        ->and($retrieved->end_date->toDateString())->toBe('2025-10-20')
        ->and($retrieved->status)->toBe('in_progress');
});

test('can create work order with future dates', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);

    $futureStartDate = now()->addDays(10)->format('Y-m-d');
    $futureEndDate = now()->addDays(20)->format('Y-m-d');

    $workOrderData = [
        'number' => WorkOrder::nextNumber(),
        'title' => 'Projeto Futuro',
        'description' => 'Projeto agendado para o futuro',
        'client_id' => $client->id,
        'priority' => 'low',
        'start_date' => $futureStartDate,
        'end_date' => $futureEndDate,
        'status' => 'pending',
    ];

    $response = $this->post(route('work-orders.store'), $workOrderData);
    $response->assertRedirect();

    assertDatabaseHas('work_orders', [
        'title' => 'Projeto Futuro',
        'start_date' => $futureStartDate,
        'end_date' => $futureEndDate,
    ]);
});

test('can create work order with same start and end date', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);

    $sameDate = '2025-10-13';

    $workOrderData = [
        'number' => WorkOrder::nextNumber(),
        'title' => 'Tarefa Pontual',
        'description' => 'Tarefa de um dia',
        'client_id' => $client->id,
        'priority' => 'high',
        'start_date' => $sameDate,
        'end_date' => $sameDate,
        'status' => 'pending',
    ];

    $response = $this->post(route('work-orders.store'), $workOrderData);
    $response->assertRedirect();

    $workOrder = WorkOrder::where('title', 'Tarefa Pontual')->first();
    expect($workOrder->start_date->toDateString())->toBe($sameDate)
        ->and($workOrder->end_date->toDateString())->toBe($sameDate);
});

test('date fields are nullable', function () {
    actingAs($this->user);

    $client = Entity::factory()->create(['types' => ['client']]);

    $workOrderData = [
        'number' => WorkOrder::nextNumber(),
        'title' => 'Sem Datas',
        'description' => 'Work order sem datas definidas',
        'client_id' => $client->id,
        'priority' => 'medium',
        'status' => 'pending',
        // Sem start_date e end_date
    ];

    $response = $this->post(route('work-orders.store'), $workOrderData);
    $response->assertRedirect();

    $workOrder = WorkOrder::where('title', 'Sem Datas')->first();
    expect($workOrder)->not->toBeNull()
        ->and($workOrder->start_date)->toBeNull()
        ->and($workOrder->end_date)->toBeNull();
});

