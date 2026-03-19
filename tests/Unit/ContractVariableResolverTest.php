<?php

declare(strict_types=1);

use App\MaritalStatus;
use App\Models\Contratante;
use App\Services\ContractVariableResolver;

it('replaces contratante variables in a contract body', function () {
    $contratante = new Contratante([
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

    $body = 'Eu, $contratante.nome, CPF $contratante.cpf, RG $contratante.rg, nascido em $contratante.nascimento, '
        . '$contratante.nacionalidade, $contratante.estado_civil, $contratante.profissao, '
        . 'residente em $contratante.endereco, nº $contratante.endereco_numero, $contratante.endereco_complemento, '
        . 'bairro $contratante.bairro, $contratante.cidade - $contratante.estado, CEP $contratante.cep, '
        . 'e-mail $contratante.email, telefone $contratante.telefone.';

    $resolver = new ContractVariableResolver;
    $result = $resolver->resolve($body, $contratante);

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
        ->not->toContain('$contratante.');
});

it('keeps variables when contratante field is empty', function () {
    $contratante = new Contratante([
        'name' => 'Maria',
        'email' => 'maria@email.com',
        'cpf' => '111.222.333-44',
    ]);

    $body = 'Contratante: $contratante.nome, Profissão: $contratante.profissao, RG: $contratante.rg';

    $resolver = new ContractVariableResolver;
    $result = $resolver->resolve($body, $contratante);

    expect($result)
        ->toContain('Maria')
        ->toContain('$contratante.profissao')
        ->toContain('$contratante.rg');
});

it('lists unresolved variables in a body', function () {
    $contratante = new Contratante([
        'name' => 'João',
        'email' => 'joao@email.com',
        'cpf' => '123.456.789-00',
    ]);

    $body = 'Nome: $contratante.nome, RG: $contratante.rg, Profissão: $contratante.profissao';

    $resolver = new ContractVariableResolver;
    $unresolved = $resolver->unresolvedVariables($body, $contratante);

    expect($unresolved)
        ->toContain('$contratante.rg')
        ->toContain('$contratante.profissao')
        ->not->toContain('$contratante.nome');
});
