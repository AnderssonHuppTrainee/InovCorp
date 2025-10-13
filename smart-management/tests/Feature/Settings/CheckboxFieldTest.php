<?php

use App\Models\System\TaxRate;
use App\Models\System\Country;
use App\Models\System\ContactRole;
use App\Models\System\CalendarAction;
use App\Models\System\CalendarEventType;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('can create tax rate with is_active checkbox', function () {
    actingAs($this->user);

    $taxRateData = [
        'name' => 'IVA 23%',
        'rate' => 23,
        'is_active' => true,
    ];

    $response = $this->post(route('tax-rates.store'), $taxRateData);

    $response->assertRedirect();

    // 🔍 TESTE CRÍTICO: Verificar que checkbox foi salvo corretamente
    assertDatabaseHas('tax_rates', [
        'name' => 'IVA 23%',
        'rate' => 23,
        'is_active' => 1,
    ]);
});

test('can create tax rate with is_active = false', function () {
    actingAs($this->user);

    $taxRateData = [
        'name' => 'IVA Antigo',
        'rate' => 21,
        'is_active' => false,
    ];

    $response = $this->post(route('tax-rates.store'), $taxRateData);

    assertDatabaseHas('tax_rates', [
        'name' => 'IVA Antigo',
        'is_active' => 0,
    ]);
});

test('can update tax rate is_active status', function () {
    actingAs($this->user);

    $taxRate = TaxRate::factory()->create([
        'name' => 'IVA Test',
        'rate' => 23,
        'is_active' => true,
    ]);

    $updateData = [
        'name' => 'IVA Test',
        'rate' => 23,
        'is_active' => false,
    ];

    $response = $this->put(route('tax-rates.update', $taxRate->id), $updateData);

    assertDatabaseHas('tax_rates', [
        'id' => $taxRate->id,
        'is_active' => 0,
    ]);
});

test('can create country with is_active checkbox', function () {
    actingAs($this->user);

    $countryData = [
        'name' => 'Portugal',
        'code' => 'PT',
        'phone_code' => '+351',
        'is_active' => true,
    ];

    $response = $this->post(route('countries.store'), $countryData);

    assertDatabaseHas('countries', [
        'name' => 'Portugal',
        'code' => 'PT',
        'is_active' => 1,
    ]);
});

test('can create contact role with is_active checkbox', function () {
    actingAs($this->user);

    $roleData = [
        'name' => 'Gerente',
        'description' => 'Gerente de conta',
        'is_active' => true,
    ];

    $response = $this->post(route('contact-roles.store'), $roleData);

    assertDatabaseHas('contact_roles', [
        'name' => 'Gerente',
        'is_active' => 1,
    ]);
});

test('can create calendar action with is_active checkbox', function () {
    actingAs($this->user);

    $actionData = [
        'name' => 'Chamada Telefónica',
        'color' => '#FF5733',
        'is_active' => true,
    ];

    $response = $this->post(route('calendar-actions.store'), $actionData);

    assertDatabaseHas('calendar_actions', [
        'name' => 'Chamada Telefónica',
        'is_active' => 1,
    ]);
});

test('can create calendar event type with is_active checkbox', function () {
    actingAs($this->user);

    $eventTypeData = [
        'name' => 'Reunião Cliente',
        'color' => '#3498db',
        'is_active' => true,
    ];

    $response = $this->post(route('calendar-event-types.store'), $eventTypeData);

    assertDatabaseHas('calendar_event_types', [
        'name' => 'Reunião Cliente',
        'is_active' => 1,
    ]);
});

test('checkbox defaults to false when not provided', function () {
    actingAs($this->user);

    $taxRateData = [
        'name' => 'IVA Desativado',
        'rate' => 6,
        // is_active não enviado
    ];

    $response = $this->post(route('tax-rates.store'), $taxRateData);

    $taxRate = TaxRate::where('name', 'IVA Desativado')->first();

    // Quando checkbox não é enviado, deve ser false/0
    expect($taxRate->is_active)->toBeFalse();
});

test('can toggle checkbox multiple times', function () {
    actingAs($this->user);

    $taxRate = TaxRate::factory()->create([
        'name' => 'Toggle Test',
        'rate' => 23,
        'is_active' => false,
    ]);

    // Ativar
    $this->put(route('settings.tax-rates.update', $taxRate->id), [
        'name' => 'Toggle Test',
        'rate' => 23,
        'is_active' => true,
    ]);
    expect($taxRate->fresh()->is_active)->toBeTrue();

    // Desativar
    $this->put(route('settings.tax-rates.update', $taxRate->id), [
        'name' => 'Toggle Test',
        'rate' => 23,
        'is_active' => false,
    ]);
    expect($taxRate->fresh()->is_active)->toBeFalse();

    // Ativar novamente
    $this->put(route('settings.tax-rates.update', $taxRate->id), [
        'name' => 'Toggle Test',
        'rate' => 23,
        'is_active' => true,
    ]);
    expect($taxRate->fresh()->is_active)->toBeTrue();
});

test('checkbox accepts various truthy values', function () {
    actingAs($this->user);

    // Teste com 1 (integer)
    $taxRate1 = TaxRate::create([
        'name' => 'Test 1',
        'rate' => 23,
        'is_active' => 1,
    ]);
    expect($taxRate1->is_active)->toBeTrue();

    // Teste com true (boolean)
    $taxRate2 = TaxRate::create([
        'name' => 'Test 2',
        'rate' => 23,
        'is_active' => true,
    ]);
    expect($taxRate2->is_active)->toBeTrue();

    // Teste com "1" (string)
    $taxRate3 = TaxRate::create([
        'name' => 'Test 3',
        'rate' => 23,
        'is_active' => '1',
    ]);
    expect($taxRate3->is_active)->toBeTrue();
});

test('checkbox accepts various falsy values', function () {
    actingAs($this->user);

    // Teste com 0 (integer)
    $taxRate1 = TaxRate::create([
        'name' => 'Test 0',
        'rate' => 23,
        'is_active' => 0,
    ]);
    expect($taxRate1->is_active)->toBeFalse();

    // Teste com false (boolean)
    $taxRate2 = TaxRate::create([
        'name' => 'Test false',
        'rate' => 23,
        'is_active' => false,
    ]);
    expect($taxRate2->is_active)->toBeFalse();

    // Teste com "0" (string)
    $taxRate3 = TaxRate::create([
        'name' => 'Test string 0',
        'rate' => 23,
        'is_active' => '0',
    ]);
    expect($taxRate3->is_active)->toBeFalse();
});

