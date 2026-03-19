<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\ContratanteContract;
use App\Models\ObjetoContrato;
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

it('resolves contratado variables when generating a contratante contract', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create(['name' => 'João Contratante']);
    $contratado = Contratado::factory()->for($user)->create(['name' => 'Ana Contratada', 'cpf' => '222.222.222-22']);
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Contratante: $contratante.nome. Contratado: $contratado.nome, CPF $contratado.cpf.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante, $contratado);

    expect($contratanteContract)
        ->body->toContain('João Contratante')
        ->body->toContain('Ana Contratada')
        ->body->toContain('222.222.222-22')
        ->body->not->toContain('$contratado.nome')
        ->body->not->toContain('$contratado.cpf')
        ->contratado_id->toBe($contratado->id);
});

it('resolves objeto contrato variables when generating a contratante contract', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create();
    $contratado = Contratado::factory()->for($user)->create();
    $objeto = ObjetoContrato::factory()->create([
        'contratante_id' => $contratante->id,
        'contratado_id' => $contratado->id,
        'descricao' => 'Desenvolvimento de Software',
        'valor' => '5000.00',
    ]);
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Objeto: $objeto.descricao pelo valor de R$ $objeto.valor.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante, $contratado, $objeto);

    expect($contratanteContract)
        ->body->toContain('Desenvolvimento de Software')
        ->body->toContain('5000.00')
        ->body->not->toContain('$objeto.descricao')
        ->objeto_contrato_id->toBe($objeto->id);
});

it('regenerates resolving contratado and objeto contrato variables', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create(['name' => 'Maria Santos']);
    $contratado = Contratado::factory()->for($user)->create(['name' => 'Carlos Contratado']);
    $objeto = ObjetoContrato::factory()->create([
        'contratante_id' => $contratante->id,
        'contratado_id' => $contratado->id,
        'descricao' => 'Serviço de TI',
    ]);
    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>$contratante.nome contrata $contratado.nome para $objeto.descricao.</p>',
    ]);

    $generator = app(ClientContractGenerator::class);
    $contratanteContract = $generator->generate($contract, $contratante, $contratado, $objeto);

    $contratanteContract->update(['body' => 'Editado manualmente', 'is_manually_edited' => true]);

    $regenerated = $generator->regenerate($contratanteContract);

    expect($regenerated)
        ->body->toContain('Maria Santos')
        ->body->toContain('Carlos Contratado')
        ->body->toContain('Serviço de TI')
        ->body->not->toContain('Editado manualmente')
        ->is_manually_edited->toBeFalse();
});
