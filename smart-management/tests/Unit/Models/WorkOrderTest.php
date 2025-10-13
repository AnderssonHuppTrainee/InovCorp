<?php

use App\Models\Core\WorkOrder;
use App\Models\Core\Entity;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create a work order with dates', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $user = User::factory()->create();
    
    $startDate = now();
    $endDate = now()->addDays(7);

    $workOrder = WorkOrder::create([
        'number' => WorkOrder::nextNumber(),
        'title' => 'Test Work Order',
        'description' => 'Test Description',
        'client_id' => $client->id,
        'assigned_to' => $user->id,
        'priority' => 'medium',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'status' => 'pending',
    ]);

    expect($workOrder)->toBeInstanceOf(WorkOrder::class)
        ->and($workOrder->start_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($workOrder->end_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);

    // 🔍 TESTE CRÍTICO: Verificar que as datas são salvas corretamente
    $freshWorkOrder = WorkOrder::find($workOrder->id);
    expect($freshWorkOrder->start_date->toDateString())->toBe($startDate->toDateString())
        ->and($freshWorkOrder->end_date->toDateString())->toBe($endDate->toDateString());
});

test('dates are persisted to database correctly', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    
    $workOrder = WorkOrder::create([
        'number' => WorkOrder::nextNumber(),
        'title' => 'Date Persistence Test',
        'description' => 'Testing date persistence',
        'client_id' => $client->id,
        'priority' => 'high',
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
        'status' => 'pending',
    ]);

    // Buscar do banco novamente para garantir persistência
    $retrieved = WorkOrder::find($workOrder->id);
    
    expect($retrieved->start_date->toDateString())->toBe('2025-10-13')
        ->and($retrieved->end_date->toDateString())->toBe('2025-10-20');
});

test('can update work order dates', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    
    $workOrder = WorkOrder::create([
        'number' => WorkOrder::nextNumber(),
        'title' => 'Update Test',
        'description' => 'Testing date updates',
        'client_id' => $client->id,
        'priority' => 'low',
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
        'status' => 'pending',
    ]);

    // Atualizar datas
    $workOrder->update([
        'start_date' => '2025-10-15',
        'end_date' => '2025-10-25',
    ]);

    // Verificar atualização
    $updated = WorkOrder::find($workOrder->id);
    expect($updated->start_date->toDateString())->toBe('2025-10-15')
        ->and($updated->end_date->toDateString())->toBe('2025-10-25');
});

test('can filter work orders by status', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    WorkOrder::factory()->create(['client_id' => $client->id, 'status' => 'pending']);
    WorkOrder::factory()->create(['client_id' => $client->id, 'status' => 'in_progress']);
    WorkOrder::factory()->create(['client_id' => $client->id, 'status' => 'completed']);
    WorkOrder::factory()->create(['client_id' => $client->id, 'status' => 'pending']);

    expect(WorkOrder::pending()->count())->toBe(2)
        ->and(WorkOrder::inProgress()->count())->toBe(1)
        ->and(WorkOrder::completed()->count())->toBe(1);
});

test('generates next number correctly', function () {
    $firstNumber = WorkOrder::nextNumber();
    expect($firstNumber)->toBe('000001');

    WorkOrder::factory()->create(['number' => $firstNumber]);

    $secondNumber = WorkOrder::nextNumber();
    expect($secondNumber)->toBe('000002');
});

test('belongs to client and assigned user', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $user = User::factory()->create();

    $workOrder = WorkOrder::factory()->create([
        'client_id' => $client->id,
        'assigned_to' => $user->id,
    ]);

    expect($workOrder->client)->toBeInstanceOf(Entity::class)
        ->and($workOrder->client->id)->toBe($client->id)
        ->and($workOrder->assignedUser)->toBeInstanceOf(User::class)
        ->and($workOrder->assignedUser->id)->toBe($user->id);
});

