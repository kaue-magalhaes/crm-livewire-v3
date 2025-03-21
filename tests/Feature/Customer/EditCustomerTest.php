<?php

use App\Livewire\Customers;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    actingAs(User::factory()->create());
    $this->customer = Customer::factory()->create();
});

it('should be able to update a customer', function () {
    Livewire::test(Customers\Update::class)
        ->call('load', $this->customer->id)
        ->set('form.name', 'John Doe')
        ->assertPropertyWired('form.name')
        ->set('form.email', 'joe@doe.com')
        ->assertPropertyWired('form.email')
        ->set('form.phone', '123456789')
        ->assertPropertyWired('form.phone')
        ->call('save')
        ->assertMethodWiredToForm('save')
        ->assertHasNoErrors();

    assertDatabaseHas('customers', [
        'id'    => $this->customer->id,
        'name'  => 'John Doe',
        'email' => 'joe@doe.com',
        'phone' => '123456789',
        'type'  => 'customer',
    ]);
});

describe('validations', function () {
    test('name', function ($rule, $value) {
        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.name', $value)
            ->call('save')
            ->assertHasErrors(['form.name' => $rule]);
    })->with([
        'required' => ['required', ''],
        'min'      => ['min', 'Jo'],
        'max'      => ['max', str_repeat('a', 256)],
    ]);

    test('email should be required if we dont have a phone number', function () {
        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', '')
            ->set('form.phone', '')
            ->call('save')
            ->assertHasErrors(['form.email' => 'required_without']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', '')
            ->set('form.phone', '1232132')
            ->call('save')
            ->assertHasNoErrors(['form.email' => 'required_without']);
    });

    test('email should be valid', function () {
        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'invalid-email')
            ->call('save')
            ->assertHasErrors(['form.email' => 'email']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'joe@doe.com')
            ->call('save')
            ->assertHasNoErrors(['form.email' => 'email']);
    });

    test('email should be unique', function () {
        Customer::factory()->create(['email' => 'joe@doe.com']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'joe@doe.com')
            ->call('save')
            ->assertHasErrors(['form.email' => 'unique']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', $this->customer->email)
            ->call('save')
            ->assertHasNoErrors(['form.email' => 'unique']);
    });

    test('phone should be required if email is empty', function () {
        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', '')
            ->set('form.phone', '')
            ->call('save')
            ->assertHasErrors(['form.phone' => 'required_without']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'joe@doe.com')
            ->set('form.phone', '')
            ->call('save')
            ->assertHasNoErrors(['form.phone' => 'required_without']);
    });

    test('phone should be unique', function () {
        Customer::factory()->create(['phone' => '123456789']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.phone', '123456789')
            ->call('save')
            ->assertHasErrors(['form.phone' => 'unique']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.phone', $this->customer->phone)
            ->call('save')
            ->assertHasNoErrors(['form.phone' => 'unique']);

    });
});

test('check if component is in the page', function () {
    Livewire::test(Customers\Index::class)
        ->assertContainsLivewireComponent('customers.update');
});
