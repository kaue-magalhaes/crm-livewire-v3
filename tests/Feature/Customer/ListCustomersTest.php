<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('should be able to access the route customers', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);
    get(route('customers'))
        ->assertOk();
});
