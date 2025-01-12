<?php

use App\Livewire\Auth\PasswordRecovery;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

test('needs to have a route to password recovery', function () {
    get(route('password.recovery'))
        ->assertOk();
});

test('the route password.recovery renders the Livewire component PasswordRecovery', function () {
    get(route('password.recovery'))
        ->assertSeeLivewire(PasswordRecovery::class);
});

it('should be able to request password recovery using an email and receive a password recovery notification', function () {
    Notification::fake();

    /** @var User $user */
    $user = User::factory()->create();

    Livewire::test(PasswordRecovery::class)
        ->assertDontSee('You will receive an email with the link to reset your password.')
        ->set('email', $user->email)
        ->call('requestPasswordRecovery')
        ->assertSee('You will receive an email with the link to reset your password.');

    Notification::assertSentTo(
        $user,
        ResetPassword::class
    );
});

test('validates email for password recovery', function ($field) {
    Livewire::test(PasswordRecovery::class)
        ->set('email', $field->value)
        ->call('requestPasswordRecovery')
        ->assertHasErrors(['email' => $field->rule]);
})->with([
    'required' => (object)['value' => '', 'rule' => 'required'],
    'email'    => (object)['value' => 'invalid-email', 'rule' => 'email'],
]);

test('needs to create a token for password recovery', function () {
    /** @var User $user */
    $user = User::factory()->create();

    Livewire::test(PasswordRecovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    assertDatabaseCount('password_reset_tokens', 1);
    assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
});
