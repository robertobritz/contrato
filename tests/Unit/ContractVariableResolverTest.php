<?php

declare(strict_types=1);

use App\MaritalStatus;
use App\Models\Client;
use App\Services\ContractVariableResolver;

it('replaces client variables in a contract body', function () {
    $client = new Client([
        'name' => 'João Silva',
        'email' => 'joao@email.com',
        'phone' => '(11) 99999-9999',
        'cpf' => '123.456.789-00',
        'rg' => '12.345.678-9',
        'birth_date' => '1990-05-15',
        'nationality' => 'Brasileiro',
        'marital_status' => MaritalStatus::Casado,
        'profession' => 'Engenheiro',
        'address' => 'Rua das Flores',
        'address_number' => '123',
        'address_complement' => 'Apto 4',
        'neighborhood' => 'Centro',
        'city' => 'São Paulo',
        'state' => 'SP',
        'zip_code' => '01234-567',
    ]);

    $body = 'Eu, $cliente.nome, CPF $cliente.cpf, RG $cliente.rg, nascido em $cliente.nascimento, '
        .'$cliente.nacionalidade, $cliente.estado_civil, $cliente.profissao, '
        .'residente em $cliente.endereco, nº $cliente.endereco_numero, $cliente.endereco_complemento, '
        .'bairro $cliente.bairro, $cliente.cidade - $cliente.estado, CEP $cliente.cep, '
        .'e-mail $cliente.email, telefone $cliente.telefone.';

    $resolver = new ContractVariableResolver;
    $result = $resolver->resolve($body, $client);

    expect($result)
        ->toContain('João Silva')
        ->toContain('123.456.789-00')
        ->toContain('12.345.678-9')
        ->toContain('15/05/1990')
        ->toContain('Brasileiro')
        ->toContain('Casado(a)')
        ->toContain('Engenheiro')
        ->toContain('Rua das Flores')
        ->toContain('123')
        ->toContain('Apto 4')
        ->toContain('Centro')
        ->toContain('São Paulo')
        ->toContain('SP')
        ->toContain('01234-567')
        ->toContain('joao@email.com')
        ->toContain('(11) 99999-9999')
        ->not->toContain('$cliente.');
});

it('keeps variables when client field is empty', function () {
    $client = new Client([
        'name' => 'Maria',
        'email' => 'maria@email.com',
        'cpf' => '111.222.333-44',
    ]);

    $body = 'Cliente: $cliente.nome, Profissão: $cliente.profissao, RG: $cliente.rg';

    $resolver = new ContractVariableResolver;
    $result = $resolver->resolve($body, $client);

    expect($result)
        ->toContain('Maria')
        ->toContain('$cliente.profissao')
        ->toContain('$cliente.rg');
});

it('lists unresolved variables in a body', function () {
    $client = new Client([
        'name' => 'João',
        'email' => 'joao@email.com',
        'cpf' => '123.456.789-00',
    ]);

    $body = 'Nome: $cliente.nome, RG: $cliente.rg, Profissão: $cliente.profissao';

    $resolver = new ContractVariableResolver;
    $unresolved = $resolver->unresolvedVariables($body, $client);

    expect($unresolved)
        ->toContain('$cliente.rg')
        ->toContain('$cliente.profissao')
        ->not->toContain('$cliente.nome');
});
