<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\Contratante;
use App\Models\ContratanteContract;
use App\Models\User;
use App\Services\ClientContractGenerator;

it('generates a contratante contract from a base contract', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create([
        'name' => 'João Silva',
        'cpf' => '123.456.789-00',
    ]);
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Contrato para $contratante.nome, CPF $contratante.cpf.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante);

    expect($contratanteContract)
        ->toBeInstanceOf(ContratanteContract::class)
        ->body->toContain('João Silva')
        ->body->toContain('123.456.789-00')
        ->body->not->toContain('$contratante.nome')
        ->is_manually_edited->toBeFalse()
        ->generated_at->not->toBeNull();
});

it('regenerates a contratante contract overwriting previous body', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create([
        'name' => 'Maria Santos',
        'cpf' => '987.654.321-00',
    ]);
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Contrato para $contratante.nome.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante);

    $contratanteContract->update([
        'body' => 'Editado manualmente',
        'is_manually_edited' => true,
    ]);

    $regenerated = $generator->regenerate($contratanteContract);

    expect($regenerated)
        ->body->toContain('Maria Santos')
        ->body->not->toContain('Editado manualmente')
        ->is_manually_edited->toBeFalse();
});

it('does not create duplicate contratante contracts for same contract and contratante', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create();
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Contrato para $contratante.nome.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $first = $generator->generate($contract, $contratante);
    $second = $generator->generate($contract, $contratante);

    expect($first->id)->toBe($second->id);
    expect(ContratanteContract::query()->count())->toBe(1);
});
