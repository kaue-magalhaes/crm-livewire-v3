<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
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
        ->assertRedirectToRoute('dashboard');

    assertDatabaseHas('users', [
        'name'  => 'Kauê de Magalhães',
        'email' => 'test@test.com',
    ]);

    assertDatabaseCount('users', 1);

    expect(auth()->check())
        ->and(auth()->user()->id)->toBe(User::first()->id);
});

test('validation rules', function ($field) {
    if ($field->rule == 'unique') {
        User::factory()->create([
            $field->label => $field->value,
        ]);
    }

    $livewireTest = Livewire::test(Register::class)
        ->set($field->label, $field->value);

    if (property_exists($field, 'additionalValue')) {
        $livewireTest->set($field->additionalValueLabel, $field->additionalValue);
    }

    $livewireTest->call('submit')
        ->assertHasErrors([$field->label => $field->rule]);
})->with([
    'name::required'     => (object)['label' => 'name', 'value' => '', 'rule' => 'required'],
    'name::max:255'      => (object)['label' => 'name', 'value' => str_repeat('*', 256), 'rule' => 'max'],
    'email::required'    => (object)['label' => 'email', 'value' => '', 'rule' => 'required'],
    'email::email'       => (object)['label' => 'email', 'value' => 'not-an-email', 'rule' => 'email'],
    'email::max:255'     => (object)['label' => 'email', 'value' => str_repeat('*', 256) . "@test.com", 'rule' => 'max'],
    'email::confirmed'   => (object)['label' => 'email', 'value' => 'test@test.com', 'rule' => 'confirmed'],
    'email::unique'      => (object)['label' => 'email', 'value' => 'test@test.com', 'rule' => 'unique', 'additionalValueLabel' => 'email_confirmation', 'additionalValue' => 'test@test.com'],
    'password::required' => (object)['label' => 'password', 'value' => '', 'rule' => 'required'],
]);

it('should be notification welcoming the new user', function () {
    Notification::fake();

    Livewire::test(Register::class)
        ->set('name', 'Kauê de Magalhães')
        ->set('email', 'test@test.com')
        ->set('email_confirmation', 'test@test.com')
        ->set('password', 'password')
        ->call('submit');

    $user = User::whereEmail('test@test.com')->first();

    Notification::assertSentTo($user, WelcomeNotification::class);

});

it('should dispatch Registered event', function () {
    Event::fake();

    Livewire::test(Register::class)
        ->set('name', 'Joe doe')
        ->set('email', 'joe@doe.com')
        ->set('email_confirmation', 'joe@doe.com')
        ->set('password', 'password')
        ->call('submit');

    Event::assertDispatched(Registered::class);
});
