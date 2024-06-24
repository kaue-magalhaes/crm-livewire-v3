<?php

use App\Livewire\Admin\Dashboard;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('should block access to users without the permission _be an admin', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire\Livewire::test(Dashboard::class)
        ->assertForbidden();

    get(route('admin.dashboard'))
        ->assertForbidden();
});
