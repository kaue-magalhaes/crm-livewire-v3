<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Login::class)
        ->assertOk();
});

it('should be able login', function () {
    $user = User::factory()->create([
        'email'    => 'test@test.com',
        'password' => 'password',
    ]);

    Livewire::test(Login::class)
        ->set('email', 'test@test.com')
        ->set('password', 'password')
        ->call('tryToLogin')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue()
        ->and(auth()->user()->id)->toBe($user->id);
});
