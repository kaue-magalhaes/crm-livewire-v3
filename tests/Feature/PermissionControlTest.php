<?php

use App\Models\Permission;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

it('should be able to give an user a permission to de something', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $user->givePermissionTo('be an admin');

    expect($user)
        ->hasPermissionTo('be an admin')
        ->toBeTrue();

    assertDatabaseHas('permissions', [
        'key' => 'be an admin',
    ]);

    assertDatabaseHas('permission_user', [
        'user_id'       => $user->id,
        'permission_id' => Permission::query()->where(['key' => 'be an admin'])->first()->id,
    ]);
});
