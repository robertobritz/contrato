<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Contract;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    /** @var \Tests\TestCase&object{user: \App\Models\User, client: \App\Models\Client, contract: \App\Models\Contract, clientContract: \App\Models\ClientContract} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);

    $this->client = Client::factory()->for($this->user)->create(['name' => 'João Silva', 'cpf' => '111.222.333-44']);
    $this->contract = Contract::factory()->for($this->user)->create(['title' => 'Contrato de Teste', 'body' => '<p>Olá $cliente.nome.</p>']);
    $this->clientContract = ClientContract::factory()->create([
        'contract_id' => $this->contract->id,
        'client_id' => $this->client->id,
        'body' => '<p>Olá João Silva.</p>',
    ]);
});

it('exports a client contract as pdf', function () {
    $this->get(route('contracts.export.pdf', $this->clientContract))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});

it('exports a client contract as docx', function () {
    $this->get(route('contracts.export.docx', $this->clientContract))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
});

it('prevents export of another users contract as pdf', function () {
    $otherUser = User::factory()->create();
    $otherClient = Client::factory()->for($otherUser)->create();
    $otherContract = Contract::factory()->for($otherUser)->create();
    $otherClientContract = ClientContract::factory()->create([
        'contract_id' => $otherContract->id,
        'client_id' => $otherClient->id,
        'body' => '<p>Contrato alheio.</p>',
    ]);

    $this->get(route('contracts.export.pdf', $otherClientContract))
        ->assertForbidden();
});

it('prevents export of another users contract as docx', function () {
    $otherUser = User::factory()->create();
    $otherClient = Client::factory()->for($otherUser)->create();
    $otherContract = Contract::factory()->for($otherUser)->create();
    $otherClientContract = ClientContract::factory()->create([
        'contract_id' => $otherContract->id,
        'client_id' => $otherClient->id,
        'body' => '<p>Contrato alheio.</p>',
    ]);

    $this->get(route('contracts.export.docx', $otherClientContract))
        ->assertForbidden();
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $this->get(route('contracts.export.pdf', $this->clientContract))
        ->assertRedirect();
});
