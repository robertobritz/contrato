<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Contract;
use App\Models\User;
use App\Services\ClientContractGenerator;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    /** @var \Tests\TestCase&object{user: \App\Models\User} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('generates a client contract when binding a client to a contract', function () {
    $client = Client::factory()->for($this->user)->create([
        'name' => 'Ana Paula',
        'cpf' => '111.222.333-44',
    ]);
    $contract = Contract::factory()->for($this->user)->create([
        'body' => '<p>Contrato para $cliente.nome, CPF $cliente.cpf.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $clientContract = $generator->generate($contract, $client);

    expect($clientContract->body)
        ->toContain('Ana Paula')
        ->toContain('111.222.333-44')
        ->not->toContain('$cliente.nome');
});

it('marks manually edited contracts', function () {
    $client = Client::factory()->for($this->user)->create();
    $contract = Contract::factory()->for($this->user)->create();

    $generator = app(ClientContractGenerator::class);
    $clientContract = $generator->generate($contract, $client);

    expect($clientContract->is_manually_edited)->toBeFalse();

    $clientContract->update([
        'body' => 'Editado pelo usuário',
        'is_manually_edited' => true,
    ]);

    expect($clientContract->refresh()->is_manually_edited)->toBeTrue();
});

it('regenerates a client contract from the base contract', function () {
    $client = Client::factory()->for($this->user)->create([
        'name' => 'Roberto',
    ]);
    $contract = Contract::factory()->for($this->user)->create([
        'body' => '<p>Contrato para $cliente.nome.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $clientContract = $generator->generate($contract, $client);

    $clientContract->update([
        'body' => 'Texto editado manualmente',
        'is_manually_edited' => true,
    ]);

    $regenerated = $generator->regenerate($clientContract);

    expect($regenerated)
        ->body->toContain('Roberto')
        ->body->not->toContain('Texto editado manualmente')
        ->is_manually_edited->toBeFalse()
        ->generated_at->not->toBeNull();
});

it('does not create duplicate bindings for same client and contract', function () {
    $client = Client::factory()->for($this->user)->create();
    $contract = Contract::factory()->for($this->user)->create();

    $generator = app(ClientContractGenerator::class);
    $generator->generate($contract, $client);
    $generator->generate($contract, $client);

    expect(ClientContract::query()->count())->toBe(1);
});

it('cascades deletion when contract is deleted', function () {
    $client = Client::factory()->for($this->user)->create();
    $contract = Contract::factory()->for($this->user)->create();

    $generator = app(ClientContractGenerator::class);
    $generator->generate($contract, $client);

    expect(ClientContract::query()->count())->toBe(1);

    $contract->delete();

    expect(ClientContract::query()->count())->toBe(0);
});
