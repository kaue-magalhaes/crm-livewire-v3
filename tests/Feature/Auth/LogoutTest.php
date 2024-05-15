<?php

use App\Livewire\Auth\Logout;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\assertGuest;

it('renders successfully', function () {
    Livewire::test(Logout::class)
        ->assertStatus(200);
});

it('should be able to logout of the application', function () {
    $user = User::factory()->create();

    Livewire::test(Logout::class)
        ->call('logout')
        ->assertRedirect(route('login'));

    assertGuest();
});
