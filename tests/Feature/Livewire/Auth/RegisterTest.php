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
        ->assertHasNoErrors()
        ->assertRedirectToRoute('home');

    assertDatabaseHas('users', [
        'name'  => 'Kauê de Magalhães',
        'email' => 'test@test.com',
    ]);

    assertDatabaseCount('users', 1);

    expect(auth()->check())
        ->and(auth()->user()->id)->toBe(\App\Models\User::first()->id);
});

test('validation rules', function ($field) {
    Livewire::test(Register::class)
        ->set($field->label, $field->value)
        ->call('submit')
        ->assertHasErrors([$field->label => $field->rule]);
})->with([
    'name::required'     => (object)['label' => 'name', 'value' => '', 'rule' => 'required'],
    'name::max:255'      => (object)['label' => 'name', 'value' => str_repeat('*', 256), 'rule' => 'max'],
    'email::required'    => (object)['label' => 'email', 'value' => '', 'rule' => 'required'],
    'email::email'       => (object)['label' => 'email', 'value' => 'not-an-email', 'rule' => 'email'],
    'email::max:255'     => (object)['label' => 'email', 'value' => str_repeat('*', 256) . "@test.com", 'rule' => 'max'],
    'email::confirmed'   => (object)['label' => 'email', 'value' => 'test@test.com', 'rule' => 'confirmed'],
    'password::required' => (object)['label' => 'password', 'value' => '', 'rule' => 'required'],
]);
