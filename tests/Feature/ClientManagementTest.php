<?php

declare(strict_types=1);

use App\Filament\Resources\Contratantes\Pages\CreateContratante;
use App\Filament\Resources\Contratantes\Pages\EditContratante;
use App\Filament\Resources\Contratantes\Pages\ListContratantes;
use App\Models\Contratante;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    /** @var TestCase&object{user: User} $this */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can list contratantes', function () {
    $contratantes = Contratante::factory()
        ->count(3)
        ->for($this->user)
        ->create();

    Livewire::test(ListContratantes::class)
        ->assertOk()
        ->assertCanSeeTableRecords($contratantes);
});

it('cannot see other users contratantes', function () {
    $otherUser = User::factory()->create();
    $otherContratante = Contratante::factory()->for($otherUser)->create();

    Livewire::test(ListContratantes::class)
        ->assertCanNotSeeTableRecords([$otherContratante]);
});

it('can create a contratante', function () {
    $newContratante = Contratante::factory()->make();

    Livewire::test(CreateContratante::class)
        ->fillForm([
            'name' => $newContratante->name,
            'email' => $newContratante->email,
            'cpf' => $newContratante->cpf,
            'phone' => $newContratante->phone,
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(Contratante::class, [
        'name' => $newContratante->name,
        'email' => $newContratante->email,
        'user_id' => $this->user->id,
    ]);
});

it('can edit a contratante', function () {
    $contratante = Contratante::factory()->for($this->user)->create();

    Livewire::test(EditContratante::class, ['record' => $contratante->getRouteKey()])
        ->fillForm([
            'name' => 'Nome Atualizado',
        ])
        ->call('save')
        ->assertNotified();

    expect($contratante->refresh()->name)->toBe('Nome Atualizado');
});

it('validates required fields on create', function () {
    Livewire::test(CreateContratante::class)
        ->fillForm([
            'name' => null,
            'email' => null,
            'cpf' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['name', 'email', 'cpf']);
});

it('prevents unauthenticated users from accessing contratantes', function () {
    auth()->logout();

    $this->get(route('filament.admin.resources.contratantes.index'))
        ->assertRedirect();
});
