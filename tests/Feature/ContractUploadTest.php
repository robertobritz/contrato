<?php

declare(strict_types=1);

use App\ContractSourceType;
use App\Filament\Resources\Contracts\Pages\CreateContract;
use App\Filament\Resources\Contracts\Pages\EditContract;
use App\Filament\Resources\Contracts\Pages\ListContracts;
use App\Models\Contract;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    /** @var TestCase&object{user: User} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can list contracts', function () {
    $contracts = Contract::factory()
        ->count(3)
        ->for($this->user)
        ->create();

    Livewire::test(ListContracts::class)
        ->assertOk()
        ->assertCanSeeTableRecords($contracts);
});

it('cannot see other users contracts', function () {
    $otherUser = User::factory()->create();
    $otherContract = Contract::factory()->for($otherUser)->create();

    Livewire::test(ListContracts::class)
        ->assertCanNotSeeTableRecords([$otherContract]);
});

it('can create a contract in manual mode', function () {
    Livewire::test(CreateContract::class)
        ->fillForm([
            'title' => 'Contrato de Locação',
            'source_type' => ContractSourceType::Manual->value,
            'body' => '<p>Contrato para $cliente.nome</p>',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(Contract::class, [
        'title' => 'Contrato de Locação',
        'user_id' => $this->user->id,
        'source_type' => ContractSourceType::Manual->value,
        'original_file_path' => null,
    ]);
});

it('sets source_type to manual when creating without upload', function () {
    Livewire::test(CreateContract::class)
        ->fillForm([
            'title' => 'Contrato Manual',
            'source_type' => ContractSourceType::Manual->value,
            'body' => '<p>Texto do contrato</p>',
        ])
        ->call('create')
        ->assertNotified();

    $contract = Contract::where('title', 'Contrato Manual')->first();

    expect($contract->source_type)->toBe(ContractSourceType::Manual);
});

it('can edit a contract body', function () {
    $contract = Contract::factory()->for($this->user)->create();

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->fillForm([
            'body' => '<p>Novo conteúdo com $cliente.cpf</p>',
        ])
        ->call('save')
        ->assertNotified();

    expect($contract->refresh()->body)->toContain('$cliente.cpf');
});

it('validates required fields', function () {
    Livewire::test(CreateContract::class)
        ->fillForm([
            'title' => null,
            'body' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title', 'body']);
});
