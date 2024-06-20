<?php

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\seed;

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

test('permission has to have a seeder', function () {
    seed(PermissionSeeder::class);

    assertDatabaseHas('permissions', [
        'key' => 'be an admin',
    ]);
});

test('seed with an admin user', function () {
    seed([PermissionSeeder::class, UserSeeder::class]);

    assertDatabaseHas('permissions', [
        'key' => 'be an admin',
    ]);

    assertDatabaseHas('permission_user', [
        'user_id'       => User::query()->first()->id,
        'permission_id' => Permission::query()->where(['key' => 'be an admin'])->first()->id,
    ]);
});
