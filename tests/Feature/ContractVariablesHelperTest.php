<?php

declare(strict_types=1);

use App\Filament\Resources\Contracts\Pages\CreateContract;
use App\Filament\Resources\Contracts\Pages\EditContract;
use App\Models\Contract;
use App\Models\Contratante;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    /** @var TestCase&object{user: User} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('returns all 16 contratante variables with labels', function () {
    $variables = Contratante::availableVariableLabels();

    expect($variables)
        ->toBeArray()
        ->toHaveCount(16)
        ->toHaveKey('$contratante.nome')
        ->toHaveKey('$contratante.email')
        ->toHaveKey('$contratante.telefone')
        ->toHaveKey('$contratante.cpf')
        ->toHaveKey('$contratante.rg')
        ->toHaveKey('$contratante.nascimento')
        ->toHaveKey('$contratante.nacionalidade')
        ->toHaveKey('$contratante.estado_civil')
        ->toHaveKey('$contratante.profissao')
        ->toHaveKey('$contratante.endereco')
        ->toHaveKey('$contratante.endereco_numero')
        ->toHaveKey('$contratante.endereco_complemento')
        ->toHaveKey('$contratante.bairro')
        ->toHaveKey('$contratante.cidade')
        ->toHaveKey('$contratante.estado')
        ->toHaveKey('$contratante.cep');
});

it('available variable labels keys match variable map keys', function () {
    $contratante = Contratante::factory()->for($this->user)->create();

    $helperKeys = array_keys(Contratante::availableVariableLabels());
    $mapKeys = array_keys($contratante->variableMap());

    expect($helperKeys)->toBe($mapKeys);
});

it('shows contract body editor on edit contract form', function () {
    $contract = Contract::factory()->for($this->user)->create();

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->assertFormFieldExists('body')
        ->assertOk();
});

it('renders floating variables panel without inline variable code display', function () {
    $this->view('filament.contract-variables-floating')
        ->assertSee('Variáveis de Contratante Disponíveis')
        ->assertSee('Inserir variável')
        ->assertSee('data-variable="$contratante.nome"', false)
        ->assertSee('data-variable="$contratante.cpf"', false)
        ->assertDontSee('<code', false);
});

it('renders floating variables panel with usage counter markup', function () {
    $this->view('filament.contract-variables-floating')
        ->assertSee('x-text', false)
        ->assertSee('updateCounts', false)
        ->assertSee('setInterval', false);
});

it('renders floating variables button on create contract page', function () {
    $contract = Contract::factory()->for($this->user)->create();

    Livewire::test(CreateContract::class)
        ->assertOk();
});

it('renders floating variables button on edit contract page', function () {
    $contract = Contract::factory()->for($this->user)->create();

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->assertOk();
});
