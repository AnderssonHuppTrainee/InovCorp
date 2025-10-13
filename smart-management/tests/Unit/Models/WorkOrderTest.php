<?php

use App\Models\Core\WorkOrder;
use App\Models\Core\Entity;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pode criar uma ordem de trabalho', function () {
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


    $freshWorkOrder = WorkOrder::find($workOrder->id);
    expect($freshWorkOrder->start_date->toDateString())->toBe($startDate->toDateString())
        ->and($freshWorkOrder->end_date->toDateString())->toBe($endDate->toDateString());
});

test('datas são salvas corretamente', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $user = User::factory()->create();

    $workOrder = WorkOrder::create([
        'number' => WorkOrder::nextNumber(),
        'title' => 'Date Persistence Test',
        'description' => 'Testing date persistence',
        'client_id' => $client->id,
        'assigned_to' => $user->id,
        'priority' => 'high',
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
        'status' => 'pending',
    ]);


    $retrieved = WorkOrder::find($workOrder->id);

    expect($retrieved->start_date->toDateString())->toBe('2025-10-13')
        ->and($retrieved->end_date->toDateString())->toBe('2025-10-20');
});

test('pode atualizar ordens de trabalho', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $user = User::factory()->create();

    $workOrder = WorkOrder::create([
        'number' => WorkOrder::nextNumber(),
        'title' => 'Update Test',
        'description' => 'Testing date updates',
        'client_id' => $client->id,
        'assigned_to' => $user->id,
        'priority' => 'low',
        'start_date' => '2025-10-13',
        'end_date' => '2025-10-20',
        'status' => 'pending',
    ]);


    $workOrder->update([
        'start_date' => '2025-10-15',
        'end_date' => '2025-10-25',
    ]);

    // Verificar atualização
    $updated = WorkOrder::find($workOrder->id);
    expect($updated->start_date->toDateString())->toBe('2025-10-15')
        ->and($updated->end_date->toDateString())->toBe('2025-10-25');
});

test('pode buscar pelo status', function () {
    $client = Entity::factory()->create(['types' => ['client']]);
    $user = User::factory()->create();

    WorkOrder::factory()->create(['client_id' => $client->id, 'assigned_to' => $user->id, 'status' => 'pending']);
    WorkOrder::factory()->create(['client_id' => $client->id, 'assigned_to' => $user->id, 'status' => 'in_progress']);
    WorkOrder::factory()->create(['client_id' => $client->id, 'assigned_to' => $user->id, 'status' => 'completed']);
    WorkOrder::factory()->create(['client_id' => $client->id, 'assigned_to' => $user->id, 'status' => 'pending']);

    expect(WorkOrder::pending()->count())->toBe(2)
        ->and(WorkOrder::inProgress()->count())->toBe(1)
        ->and(WorkOrder::completed()->count())->toBe(1);
});

test('gerar num sequencial correto', function () {

    $number = WorkOrder::nextNumber();
    expect($number)->toMatch('/^\d{6}$/');


    $workOrder = WorkOrder::factory()->create();
    expect($workOrder->number)->not->toBeNull();
});

test('pertece a um client e assigned user', function () {
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

