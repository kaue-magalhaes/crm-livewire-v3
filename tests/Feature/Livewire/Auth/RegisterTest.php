<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('should render the component', function () {
    Livewire::test(Register::class)
        ->assertOk();
});

it('should be able to register a user in the system', function () {
    Livewire::test(Register::class)
        ->set('name', 'Kauê de Magalhães')
        ->set('email', 'test@test.com')
        ->set('email_confirmation', 'test@test.com')
        ->set('password', 'password')
        ->call('submit')
        ->assertHasNoErrors();

    assertDatabaseHas('users', [
        'name'  => 'Kauê de Magalhães',
        'email' => 'test@test.com',
    ]);

    assertDatabaseCount('users', 1);
});
