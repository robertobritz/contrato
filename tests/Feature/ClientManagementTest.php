<?php

declare(strict_types=1);

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    /** @var \Tests\TestCase&object{user: \App\Models\User} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can list clients', function () {
    $clients = Client::factory()
        ->count(3)
        ->for($this->user)
        ->create();

    Livewire::test(ListClients::class)
        ->assertOk()
        ->assertCanSeeTableRecords($clients);
});

it('cannot see other users clients', function () {
    $otherUser = User::factory()->create();
    $otherClient = Client::factory()->for($otherUser)->create();

    Livewire::test(ListClients::class)
        ->assertCanNotSeeTableRecords([$otherClient]);
});

it('can create a client', function () {
    $newClient = Client::factory()->make();

    Livewire::test(CreateClient::class)
        ->fillForm([
            'name' => $newClient->name,
            'email' => $newClient->email,
            'cpf' => $newClient->cpf,
            'phone' => $newClient->phone,
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(Client::class, [
        'name' => $newClient->name,
        'email' => $newClient->email,
        'user_id' => $this->user->id,
    ]);
});

it('can edit a client', function () {
    $client = Client::factory()->for($this->user)->create();

    Livewire::test(EditClient::class, ['record' => $client->getRouteKey()])
        ->fillForm([
            'name' => 'Nome Atualizado',
        ])
        ->call('save')
        ->assertNotified();

    expect($client->refresh()->name)->toBe('Nome Atualizado');
});

it('validates required fields on create', function () {
    Livewire::test(CreateClient::class)
        ->fillForm([
            'name' => null,
            'email' => null,
            'cpf' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name', 'email', 'cpf']);
});

it('prevents unauthenticated users from accessing clients', function () {
    auth()->logout();

    $this->get(route('filament.admin.resources.clients.index'))
        ->assertRedirect();
});
