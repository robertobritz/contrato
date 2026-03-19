<?php

declare(strict_types=1);

use App\Filament\Resources\ContratanteContracts\Pages\CreateContratanteContract;
use App\Filament\Resources\ContratanteContracts\Pages\ListContratanteContracts;
use App\Models\Contract;
use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\ContratanteContract;
use App\Models\ObjetoContrato;
use App\Models\User;
use App\Services\ClientContractGenerator;
use Livewire\Livewire;
use Tests\TestCase;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    /** @var TestCase&object{user: User} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('generates a contratante contract when binding a contratante to a contract', function () {
    $contratante = Contratante::factory()->for($this->user)->create([
        'name' => 'Ana Paula',
        'cpf' => '111.222.333-44',
    ]);
    $contract = Contract::factory()->for($this->user)->create([
        'body' => '<p>Contrato para $contratante.nome, CPF $contratante.cpf.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante);

    expect($contratanteContract->body)
        ->toContain('Ana Paula')
        ->toContain('111.222.333-44')
        ->not->toContain('$contratante.nome');
});

it('marks manually edited contracts', function () {
    $contratante = Contratante::factory()->for($this->user)->create();
    $contract = Contract::factory()->for($this->user)->create();

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante);

    expect($contratanteContract->is_manually_edited)->toBeFalse();

    $contratanteContract->update([
        'body' => 'Editado pelo usuário',
        'is_manually_edited' => true,
    ]);

    expect($contratanteContract->refresh()->is_manually_edited)->toBeTrue();
});

it('regenerates a contratante contract from the base contract', function () {
    $contratante = Contratante::factory()->for($this->user)->create([
        'name' => 'Roberto',
    ]);
    $contract = Contract::factory()->for($this->user)->create([
        'body' => '<p>Contrato para $contratante.nome.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante);

    $contratanteContract->update([
        'body' => 'Texto editado manualmente',
        'is_manually_edited' => true,
    ]);

    $regenerated = $generator->regenerate($contratanteContract);

    expect($regenerated)
        ->body->toContain('Roberto')
        ->body->not->toContain('Texto editado manualmente')
        ->is_manually_edited->toBeFalse()
        ->generated_at->not->toBeNull();
});

it('does not create duplicate bindings for same contratante and contract', function () {
    $contratante = Contratante::factory()->for($this->user)->create();
    $contract = Contract::factory()->for($this->user)->create();

    $generator = app(ClientContractGenerator::class);
    $generator->generate($contract, $contratante);
    $generator->generate($contract, $contratante);

    expect(ContratanteContract::query()->count())->toBe(1);
});

it('cascades deletion when contract is deleted', function () {
    $contratante = Contratante::factory()->for($this->user)->create();
    $contract = Contract::factory()->for($this->user)->create();

    $generator = app(ClientContractGenerator::class);
    $generator->generate($contract, $contratante);

    expect(ContratanteContract::query()->count())->toBe(1);

    $contract->delete();

    expect(ContratanteContract::query()->count())->toBe(0);
});

it('can access the create contratante contract page', function () {
    Livewire::test(CreateContratanteContract::class)
        ->assertOk();
});

it('can create a contratante contract from the form with contratado and objeto contrato', function () {
    $contratante = Contratante::factory()->for($this->user)->create(['name' => 'Fernanda']);
    $contratado = Contratado::factory()->for($this->user)->create(['name' => 'Carlos']);
    $contrato = Contract::factory()->for($this->user)->create([
        'body' => '<p>Olá $contratante.nome.</p>',
    ]);
    $objetoContrato = ObjetoContrato::factory()->create([
        'contratante_id' => $contratante->id,
        'contratado_id' => $contratado->id,
    ]);

    Livewire::test(CreateContratanteContract::class)
        ->fillForm([
            'contract_id' => $contrato->id,
            'contratante_id' => $contratante->id,
            'contratado_id' => $contratado->id,
            'objeto_contrato_id' => $objetoContrato->id,
            'body' => '<p>Corpo do contrato</p>',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $contratanteContract = ContratanteContract::query()
        ->where('contract_id', $contrato->id)
        ->where('contratante_id', $contratante->id)
        ->where('contratado_id', $contratado->id)
        ->where('objeto_contrato_id', $objetoContrato->id)
        ->first();

    expect($contratanteContract)->not->toBeNull();
});

it('stores contratado and objeto contrato relationships on contratante contract model', function () {
    $contratante = Contratante::factory()->for($this->user)->create();
    $contratado = Contratado::factory()->for($this->user)->create();
    $contract = Contract::factory()->for($this->user)->create();
    $objetoContrato = ObjetoContrato::factory()->create([
        'contratante_id' => $contratante->id,
        'contratado_id' => $contratado->id,
    ]);

    $contratanteContract = ContratanteContract::factory()->create([
        'contract_id' => $contract->id,
        'contratante_id' => $contratante->id,
        'contratado_id' => $contratado->id,
        'objeto_contrato_id' => $objetoContrato->id,
    ]);

    expect($contratanteContract->contratado->id)->toBe($contratado->id)
        ->and($contratanteContract->objetoContrato->id)->toBe($objetoContrato->id);
});

it('shows the new contrato do contratante button in the list', function () {
    Livewire::test(ListContratanteContracts::class)
        ->assertOk();
});
