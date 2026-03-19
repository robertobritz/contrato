<?php

declare(strict_types=1);

use App\MaritalStatus;
use App\Models\Contract;
use App\Models\Contratante;
use App\Models\User;
use App\Services\ContractVariableResolver;

it('resolves all contratante variables in a contract body', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create([
        'name' => 'Carlos Eduardo',
        'email' => 'carlos@email.com',
        'phone' => '(21) 98765-4321',
        'cpf' => '111.222.333-44',
        'rg' => '11.222.333-4',
        'birth_date' => '1985-12-25',
        'nationality' => 'Brasileiro',
        'marital_status' => MaritalStatus::Casado,
        'profession' => 'Advogado',
        'address' => 'Av. Paulista',
        'address_number' => '1000',
        'address_complement' => 'Sala 501',
        'neighborhood' => 'Bela Vista',
        'city' => 'São Paulo',
        'state' => 'SP',
        'zip_code' => '01310-100',
    ]);

    $contract = Contract::factory()->for($user)->create([
        'body' => '<p>Eu, $contratante.nome, $contratante.nacionalidade, $contratante.estado_civil, $contratante.profissao, '
            . 'portador do CPF nº $contratante.cpf e RG nº $contratante.rg, nascido em $contratante.nascimento, '
            . 'residente na $contratante.endereco, nº $contratante.endereco_numero, $contratante.endereco_complemento, '
            . 'bairro $contratante.bairro, $contratante.cidade/$contratante.estado, CEP $contratante.cep, '
            . 'e-mail $contratante.email, telefone $contratante.telefone.</p>',
    ]);

    $resolver = app(ContractVariableResolver::class);
    $result = $resolver->resolve($contract->body, $contratante);

    expect($result)
        ->toContain('Carlos Eduardo')
        ->toContain('Brasileiro')
        ->toContain('Casado(a)')
        ->toContain('Advogado')
        ->toContain('111.222.333-44')
        ->toContain('11.222.333-4')
        ->toContain('25/12/1985')
        ->toContain('Av. Paulista')
        ->toContain('1000')
        ->toContain('Sala 501')
        ->toContain('Bela Vista')
        ->toContain('São Paulo')
        ->toContain('SP')
        ->toContain('01310-100')
        ->toContain('carlos@email.com')
        ->toContain('(21) 98765-4321')
        ->not->toContain('$contratante.');
});

it('keeps unresolved variables when contratante fields are empty', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create([
        'name' => 'Maria',
        'email' => 'maria@email.com',
        'cpf' => '999.888.777-66',
        'rg' => null,
        'profession' => null,
    ]);

    $body = 'Nome: $contratante.nome, RG: $contratante.rg, Profissão: $contratante.profissao';

    $resolver = app(ContractVariableResolver::class);
    $result = $resolver->resolve($body, $contratante);

    expect($result)
        ->toContain('Maria')
        ->toContain('$contratante.rg')
        ->toContain('$contratante.profissao');
});

it('detects unresolved variables', function () {
    $user = User::factory()->create();
    $contratante = Contratante::factory()->for($user)->create([
        'name' => 'Pedro',
        'email' => 'pedro@email.com',
        'cpf' => '555.666.777-88',
        'rg' => null,
        'birth_date' => null,
    ]);

    $body = 'Nome: $contratante.nome, RG: $contratante.rg, Nascimento: $contratante.nascimento';

    $resolver = app(ContractVariableResolver::class);
    $unresolved = $resolver->unresolvedVariables($body, $contratante);

    expect($unresolved)
        ->toContain('$contratante.rg')
        ->toContain('$contratante.nascimento')
        ->not->toContain('$contratante.nome');
});
