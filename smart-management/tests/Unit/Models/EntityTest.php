<?php

use App\Models\Core\Entity;
use App\Models\Catalog\Country;
use App\Models\Core\Contact;
use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Order\Order;
use App\Models\Core\Order\SupplierOrder;
use App\Models\Financial\Invoice\SupplierInvoice;
use App\Models\Core\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pode criar entity como cliente', function () {
    $country = Country::factory()->create();

    $entity = Entity::create([
        'number' => Entity::nextNumber(),
        'tax_number' => Entity::generatePortugueseNif(),
        'types' => ['client'],
        'name' => 'Cliente Teste Lda',
        'address' => 'Rua Teste, 123',
        'postal_code' => '1000-001',
        'city' => 'Lisboa',
        'country_id' => $country->id,
        'phone' => '+351210000000',
        'email' => 'cliente@teste.pt',
        'gdpr_consent' => true,
        'status' => 'active',
    ]);

    expect($entity)->toBeInstanceOf(Entity::class)
        ->and($entity->types)->toBe(['client'])
        ->and($entity->status)->toBe('active')
        ->and($entity->gdpr_consent)->toBeTrue();
});

test('pode criar entity como fornecedor', function () {
    $country = Country::factory()->create();

    $entity = Entity::create([
        'number' => Entity::nextNumber(),
        'tax_number' => Entity::generatePortugueseNif(),
        'types' => ['supplier'],
        'name' => 'Fornecedor Teste SA',
        'address' => 'Av. Fornecedor, 456',
        'postal_code' => '2000-001',
        'city' => 'Porto',
        'country_id' => $country->id,
        'phone' => '+351220000000',
        'email' => 'fornecedor@teste.pt',
        'gdpr_consent' => true,
        'status' => 'active',
    ]);

    expect($entity)->toBeInstanceOf(Entity::class)
        ->and($entity->types)->toBe(['supplier'])
        ->and($entity->status)->toBe('active');
});

test('pode criar entity com multiplos tipos', function () {
    $country = Country::factory()->create();

    $entity = Entity::create([
        'number' => Entity::nextNumber(),
        'tax_number' => Entity::generatePortugueseNif(),
        'types' => ['client', 'supplier'],  // ambos
        'name' => 'Empresa Multi-Tipo Lda',
        'address' => 'Rua Multi, 789',
        'postal_code' => '3000-001',
        'city' => 'Coimbra',
        'country_id' => $country->id,
        'phone' => '+351239000000',
        'email' => 'multi@teste.pt',
        'gdpr_consent' => true,
        'status' => 'active',
    ]);

    expect($entity->types)->toBeArray()
        ->and($entity->types)->toContain('client')
        ->and($entity->types)->toContain('supplier')
        ->and(count($entity->types))->toBe(2);
});

test('scope clients retorna apenas clientes', function () {
    $country = Country::factory()->create();

    Entity::factory()->create(['types' => ['client'], 'country_id' => $country->id]);
    Entity::factory()->create(['types' => ['supplier'], 'country_id' => $country->id]);
    Entity::factory()->create(['types' => ['client', 'supplier'], 'country_id' => $country->id]);

    $clients = Entity::clients()->get();

    expect($clients)->toHaveCount(2);
});

test('scope suppliers retorna apenas fornecedores', function () {
    $country = Country::factory()->create();

    Entity::factory()->create(['types' => ['client'], 'country_id' => $country->id]);
    Entity::factory()->create(['types' => ['supplier'], 'country_id' => $country->id]);
    Entity::factory()->create(['types' => ['client', 'supplier'], 'country_id' => $country->id]);

    $suppliers = Entity::suppliers()->get();

    expect($suppliers)->toHaveCount(2);
});

test('scope active retorna apenas entidades ativas', function () {
    $country = Country::factory()->create();

    Entity::factory()->create(['types' => ['client'], 'country_id' => $country->id, 'status' => 'active']);
    Entity::factory()->create(['types' => ['client'], 'country_id' => $country->id, 'status' => 'inactive']);
    Entity::factory()->create(['types' => ['client'], 'country_id' => $country->id, 'status' => 'active']);

    $active = Entity::active()->get();

    expect($active)->toHaveCount(2);
});

test('gera NIF portugues valido', function () {
    $nif = Entity::generatePortugueseNif();


    expect($nif)->toMatch('/^\d{9}$/');


    $firstDigit = (int) $nif[0];
    expect($firstDigit)->toBeIn([1, 2, 3, 5, 6, 8]);


    $sum = 0;
    for ($i = 0; $i < 8; $i++) {
        $sum += (int) $nif[$i] * (9 - $i);
    }
    $remainder = $sum % 11;
    $checkDigit = ($remainder < 2) ? 0 : 11 - $remainder;

    expect((int) $nif[8])->toBe($checkDigit);
});

test('gera numero sequencial correto', function () {
    $number = Entity::nextNumber();
    expect($number)->toMatch('/^\d{6}$/');

    $entity = Entity::factory()->create();
    expect($entity->number)->not->toBeNull();
});

test('entity tem relacionamento com country', function () {
    $country = Country::factory()->create(['name' => 'Portugal']);

    $entity = Entity::factory()->create(['country_id' => $country->id]);

    expect($entity->country)->toBeInstanceOf(Country::class)
        ->and($entity->country->name)->toBe('Portugal');
});

test('entity pode ter multiplos contacts', function () {
    $entity = Entity::factory()->create(['types' => ['client']]);

    $contact1 = Contact::factory()->create([
        'entity_id' => $entity->id,
        'first_name' => 'João',
        'last_name' => 'Silva',
    ]);

    $contact2 = Contact::factory()->create([
        'entity_id' => $entity->id,
        'first_name' => 'Maria',
        'last_name' => 'Santos',
    ]);

    expect($entity->contacts()->count())->toBe(2)
        ->and($entity->contacts->first()->first_name)->toBe('João');
});

test('cliente pode ter proposals', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    Proposal::factory()->create(['client_id' => $client->id]);
    Proposal::factory()->create(['client_id' => $client->id]);

    expect($client->clientProposals()->count())->toBe(2);
});

test('cliente pode ter orders', function () {
    $client = Entity::factory()->create(['types' => ['client']]);

    Order::factory()->create(['client_id' => $client->id]);
    Order::factory()->create(['client_id' => $client->id]);

    expect($client->clientOrders()->count())->toBe(2);
});

test('fornecedor pode ter supplier orders', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    SupplierOrder::factory()->create(['supplier_id' => $supplier->id]);

    expect($supplier->supplierOrders()->count())->toBe(1);
});

test('fornecedor pode ter supplier invoices', function () {
    $supplier = Entity::factory()->create(['types' => ['supplier']]);

    SupplierInvoice::factory()->create(['supplier_id' => $supplier->id]);

    expect($supplier->supplierInvoices()->count())->toBe(1);
});

test('entity com soft delete pode ser restaurada', function () {
    $entity = Entity::factory()->create(['types' => ['client']]);
    $entityId = $entity->id;


    $entity->delete();


    expect(Entity::find($entityId))->toBeNull();
    expect(Entity::withTrashed()->find($entityId))->not->toBeNull();


    $entity->restore();

    expect(Entity::find($entityId))->not->toBeNull();
});

test('pode atualizar status de entity', function () {
    $entity = Entity::factory()->create(['status' => 'active']);

    expect($entity->status)->toBe('active');

    $entity->update(['status' => 'inactive']);

    expect($entity->fresh()->status)->toBe('inactive');
});

test('scope filter funciona com busca de nome', function () {
    Entity::factory()->create(['name' => 'ABC Company']);
    Entity::factory()->create(['name' => 'XYZ Corporation']);
    Entity::factory()->create(['name' => 'ABC Industries']);

    $results = Entity::filter(['search' => 'ABC'])->get();

    expect($results)->toHaveCount(2);
});

test('scope filter funciona com status', function () {
    Entity::factory()->create(['status' => 'active']);
    Entity::factory()->create(['status' => 'inactive']);
    Entity::factory()->create(['status' => 'active']);

    $results = Entity::filter(['status' => 'active'])->get();

    expect($results)->toHaveCount(2);
});

test('scope filter funciona com country', function () {
    $portugal = Country::factory()->create(['name' => 'Portugal']);
    $spain = Country::factory()->create(['name' => 'Spain']);

    Entity::factory()->create(['country_id' => $portugal->id]);
    Entity::factory()->create(['country_id' => $spain->id]);
    Entity::factory()->create(['country_id' => $portugal->id]);

    $results = Entity::filter(['country_id' => $portugal->id])->get();

    expect($results)->toHaveCount(2);
});

