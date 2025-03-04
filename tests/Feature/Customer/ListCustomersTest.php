<?php

use App\Livewire\Customers;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('should be able to access the route customers', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);
    get(route('customers'))
        ->assertOk();
});

test("let's create a livewire component to list all customers in the page", function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);
    $customers = Customer::factory()->count(10)->create();

    $lw = Livewire::test(Customers\Index::class);
    $lw->assertSet('customers', function ($customers) {
        expect($customers)
            ->toHaveCount(10);

        return true;
    });

    foreach ($customers as $customer) {
        $lw->assertSee($customer->name);
    }
});

test('check the table format', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);
    Livewire::test(Customers\Index::class)
        ->assertSet('headers', [
            ['key' => 'id', 'label' => '#', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'name', 'label' => 'Name', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'email', 'label' => 'Email', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
        ]);
});

it('should be able to filter by name and email', function () {
    /** @var User $user */
    $user  = User::factory()->create();
    $joe   = Customer::factory()->create(['name' => 'Joe Doe', 'email' => 'admin@gmail.com']);
    $mario = Customer::factory()->create(['name' => 'Mario', 'email' => 'little_guy@gmail.com']);

    actingAs($user);
    Livewire::test(Customers\Index::class)
        ->assertSet('customers', function ($customers) {
            expect($customers)->toHaveCount(2);

            return true;
        })
        ->set('search', 'mar')
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->toHaveCount(1)
                ->first()->name->toBe('Mario');

            return true;
        })
        ->set('search', 'guy')
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->toHaveCount(1)
                ->first()->name->toBe('Mario');

            return true;
        });
});
