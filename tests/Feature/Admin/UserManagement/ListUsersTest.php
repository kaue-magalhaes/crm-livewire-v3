<?php

use App\Enums\Can;
use App\Livewire\Admin;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('should be able to access the route admin/users', function () {
    actingAs(User::factory()->admin()->create());

    get(route('admin.users'))
        ->assertOk();
});

test('making sure that the route is protected by the permission BE_AN_ADMIN', function () {
    actingAs(User::factory()->create());

    get(route('admin.users'))
        ->assertForbidden();
});

test("let's create a livewire component to list all users in the page", function () {
    actingAs(User::factory()->admin()->create());
    $users = User::factory()->count(5)->create();

    $lw = Livewire::test(Admin\Users\Index::class);
    $lw->assertSet('items', function ($items) {
        expect($items)
            ->toBeInstanceOf(LengthAwarePaginator::class)
            ->toHaveCount(6);

        return true;
    });
    foreach ($users as $user) {
        $lw->assertSee($user->name);
    }
});

test('check the table format', function () {
    actingAs(User::factory()->admin()->create());
    Livewire::test(Admin\Users\Index::class)
        ->assertSet('headers', [
            ['key' => 'id', 'label' => '#', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'name', 'label' => 'Name', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'email', 'label' => 'Email', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'permissions', 'label' => 'Permissions', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
        ]);
});

it('should be able to filter by name and email', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@crm.com']);
    $mario = User::factory()->create(['name' => 'Mario', 'email' => 'mario@gmail.com']);

    actingAs($admin);
    Livewire::test(Admin\Users\Index::class)
        ->assertSet('items', function ($items) {
            expect($items)->toHaveCount(2);

            return true;
        })
        ->set('search', 'mar')
        ->assertSet('items', function ($items) {
            expect($items)
                ->toHaveCount(1)
                ->first()->name->toBe('Mario');

            return true;
        })
        ->set('search', 'crm')
        ->assertSet('items', function ($items) {
            expect($items)
                ->toHaveCount(1)
                ->first()->name->toBe('Admin');

            return true;
        });
});

it('should be able to filter by permission key', function () {
    $admin      = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@crm.com']);
    $noAdmin    = User::factory()->create(['name' => 'Mario', 'email' => 'mario@gmail.com']);
    $permission = Permission::query()->where('key', Can::BE_AN_ADMIN->value)->first();

    actingAs($admin);
    Livewire::test(Admin\Users\Index::class)
        ->assertSet('items', function ($items) {
            expect($items)->toHaveCount(2);

            return true;
        })
        ->set('search_permissions', [$permission->id])
        ->assertSet('items', function ($items) {
            expect($items)
                ->toHaveCount(1)
                ->first()->name->toBe('Admin');

            return true;
        });
});

it('should be able to list deleted users', function () {
    $admin        = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@crm.com']);
    $deletedUsers = User::factory()->count(2)->create(['deleted_at' => now()]);

    actingAs($admin);
    Livewire::test(Admin\Users\Index::class)
        ->assertSet('items', function ($items) {
            expect($items)->toHaveCount(1);

            return true;
        })
        ->set('search_trash', true)
        ->assertSet('items', function ($items) {
            expect($items)
                ->toHaveCount(2);

            return true;
        });
});

it('should be able to sort by name', function () {
    $admin   = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@crm.com']);
    $noAdmin = User::factory()->create(['name' => 'Mario', 'email' => 'mario@gmail.com']);

    // Asc => Admin, Mario
    // Desc => Mario, Admin

    actingAs($admin);
    Livewire::test(Admin\Users\Index::class)
        ->set('sortDirection', 'asc')
        ->set('sortColumnBy', 'name')
        ->assertSet('items', function ($items) {
            expect($items)
                ->first()->name->toBe('Admin')
                ->and($items)->last()->name->toBe('Mario');

            return true;
        })
        ->set('sortDirection', 'desc')
        ->set('sortColumnBy', 'name')
        ->assertSet('items', function ($items) {
            expect($items)
                ->first()->name->toBe('Mario')
                ->and($items)->last()->name->toBe('Admin');

            return true;
        });
});

it('should be able to paginate the users', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@crm.com']);
    User::factory()->count(30)->create();

    actingAs($admin);
    Livewire::test(Admin\Users\Index::class)
        ->assertSet('items', function (LengthAwarePaginator $items) {
            expect($items)
                ->toHaveCount(15);

            return true;
        })
        ->set('perPage', 20)
        ->assertSet('items', function (LengthAwarePaginator $items) {
            expect($items)
                ->toHaveCount(20);

            return true;
        });
});
