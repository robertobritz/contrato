<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\Contratante;
use App\Models\ContratanteContract;
use App\Models\User;
use Tests\TestCase;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    /** @var TestCase&object{user: User, contratante: Contratante, contract: Contract, contratanteContract: ContratanteContract} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);

    $this->contratante = Contratante::factory()->for($this->user)->create(['name' => 'João Silva', 'cpf' => '111.222.333-44']);
    $this->contract = Contract::factory()->for($this->user)->create(['title' => 'Contrato de Teste', 'body' => '<p>Olá $contratante.nome.</p>']);
    $this->contratanteContract = ContratanteContract::factory()->create([
        'contract_id' => $this->contract->id,
        'contratante_id' => $this->contratante->id,
        'body' => '<p>Olá João Silva.</p>',
    ]);
});

it('exports a contratante contract as pdf', function () {
    $this->get(route('contracts.export.pdf', $this->contratanteContract))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});

it('exports a contratante contract as docx', function () {
    $this->get(route('contracts.export.docx', $this->contratanteContract))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
});

it('prevents export of another users contract as pdf', function () {
    $otherUser = User::factory()->create();
    $otherContratante = Contratante::factory()->for($otherUser)->create();
    $otherContract = Contract::factory()->for($otherUser)->create();
    $otherContratanteContract = ContratanteContract::factory()->create([
        'contract_id' => $otherContract->id,
        'contratante_id' => $otherContratante->id,
        'body' => '<p>Contrato alheio.</p>',
    ]);

    $this->get(route('contracts.export.pdf', $otherContratanteContract))
        ->assertForbidden();
});

it('prevents export of another users contract as docx', function () {
    $otherUser = User::factory()->create();
    $otherContratante = Contratante::factory()->for($otherUser)->create();
    $otherContract = Contract::factory()->for($otherUser)->create();
    $otherContratanteContract = ContratanteContract::factory()->create([
        'contract_id' => $otherContract->id,
        'contratante_id' => $otherContratante->id,
        'body' => '<p>Contrato alheio.</p>',
    ]);

    $this->get(route('contracts.export.docx', $otherContratanteContract))
        ->assertForbidden();
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $this->get(route('contracts.export.pdf', $this->contratanteContract))
        ->assertRedirect();
});
