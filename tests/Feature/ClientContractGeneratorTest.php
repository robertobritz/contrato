<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Contract;
use App\Models\User;
use App\Services\ClientContractGenerator;

it('generates a client contract from a base contract', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create([
        'name' => 'João Silva',
        'cpf' => '123.456.789-00',
    ]);
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Contrato para $cliente.nome, CPF $cliente.cpf.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $clientContract = $generator->generate($contract, $client);

    expect($clientContract)
        ->toBeInstanceOf(ClientContract::class)
        ->body->toContain('João Silva')
        ->body->toContain('123.456.789-00')
        ->body->not->toContain('$cliente.nome')
        ->is_manually_edited->toBeFalse()
        ->generated_at->not->toBeNull();
});

it('regenerates a client contract overwriting previous body', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create([
        'name' => 'Maria Santos',
        'cpf' => '987.654.321-00',
    ]);
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Contrato para $cliente.nome.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $clientContract = $generator->generate($contract, $client);

    $clientContract->update([
        'body' => 'Editado manualmente',
        'is_manually_edited' => true,
    ]);

    $regenerated = $generator->regenerate($clientContract);

    expect($regenerated)
        ->body->toContain('Maria Santos')
        ->body->not->toContain('Editado manualmente')
        ->is_manually_edited->toBeFalse();
});

it('does not create duplicate client contracts for same contract and client', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Contrato para $cliente.nome.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $first = $generator->generate($contract, $client);
    $second = $generator->generate($contract, $client);

    expect($first->id)->toBe($second->id);
    expect(ClientContract::query()->count())->toBe(1);
});
