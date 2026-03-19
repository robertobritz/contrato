<?php

declare(strict_types=1);

use App\Filament\Resources\Contracts\Pages\EditContract;
use App\Models\Client;
use App\Models\Contract;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    /** @var TestCase&object{user: User} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('returns all 16 client variables with labels', function () {
    $variables = Client::availableVariableLabels();

    expect($variables)
        ->toBeArray()
        ->toHaveCount(16)
        ->toHaveKey('$cliente.nome')
        ->toHaveKey('$cliente.email')
        ->toHaveKey('$cliente.telefone')
        ->toHaveKey('$cliente.cpf')
        ->toHaveKey('$cliente.rg')
        ->toHaveKey('$cliente.nascimento')
        ->toHaveKey('$cliente.nacionalidade')
        ->toHaveKey('$cliente.estado_civil')
        ->toHaveKey('$cliente.profissao')
        ->toHaveKey('$cliente.endereco')
        ->toHaveKey('$cliente.endereco_numero')
        ->toHaveKey('$cliente.endereco_complemento')
        ->toHaveKey('$cliente.bairro')
        ->toHaveKey('$cliente.cidade')
        ->toHaveKey('$cliente.estado')
        ->toHaveKey('$cliente.cep');
});

it('available variable labels keys match variable map keys', function () {
    $client = Client::factory()->for($this->user)->create();

    $helperKeys = array_keys(Client::availableVariableLabels());
    $mapKeys = array_keys($client->variableMap());

    expect($helperKeys)->toBe($mapKeys);
});

it('shows variables helper hint action on edit contract form', function () {
    $contract = Contract::factory()->for($this->user)->create();

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->assertFormFieldExists('body')
        ->assertOk();
});
