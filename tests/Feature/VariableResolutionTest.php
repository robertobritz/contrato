<?php

declare(strict_types=1);

use App\MaritalStatus;
use App\Models\Client;
use App\Models\Contract;
use App\Models\User;
use App\Services\ContractVariableResolver;

it('resolves all client variables in a contract body', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create([
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
        'body' => '<p>Eu, $cliente.nome, $cliente.nacionalidade, $cliente.estado_civil, $cliente.profissao, '
            .'portador do CPF nº $cliente.cpf e RG nº $cliente.rg, nascido em $cliente.nascimento, '
            .'residente na $cliente.endereco, nº $cliente.endereco_numero, $cliente.endereco_complemento, '
            .'bairro $cliente.bairro, $cliente.cidade/$cliente.estado, CEP $cliente.cep, '
            .'e-mail $cliente.email, telefone $cliente.telefone.</p>',
    ]);

    $resolver = app(ContractVariableResolver::class);
    $result = $resolver->resolve($contract->body, $client);

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
        ->not->toContain('$cliente.');
});

it('keeps unresolved variables when client fields are empty', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create([
        'name' => 'Maria',
        'email' => 'maria@email.com',
        'cpf' => '999.888.777-66',
        'rg' => null,
        'profession' => null,
    ]);

    $body = 'Nome: $cliente.nome, RG: $cliente.rg, Profissão: $cliente.profissao';

    $resolver = app(ContractVariableResolver::class);
    $result = $resolver->resolve($body, $client);

    expect($result)
        ->toContain('Maria')
        ->toContain('$cliente.rg')
        ->toContain('$cliente.profissao');
});

it('detects unresolved variables', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create([
        'name' => 'Pedro',
        'email' => 'pedro@email.com',
        'cpf' => '555.666.777-88',
        'rg' => null,
        'birth_date' => null,
    ]);

    $body = 'Nome: $cliente.nome, RG: $cliente.rg, Nascimento: $cliente.nascimento';

    $resolver = app(ContractVariableResolver::class);
    $unresolved = $resolver->unresolvedVariables($body, $client);

    expect($unresolved)
        ->toContain('$cliente.rg')
        ->toContain('$cliente.nascimento')
        ->not->toContain('$cliente.nome');
});
