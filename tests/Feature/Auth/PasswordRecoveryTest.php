<?php

use App\Livewire\Auth\PasswordRecovery;
use App\Models\User;
use App\Notifications\PasswordRecoveryNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

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
        PasswordRecoveryNotification::class
    );
});

test('validates email for password recovery', function ($value, $rule) {
    Livewire::test(PasswordRecovery::class)
        ->set('email', $value)
        ->call('requestPasswordRecovery')
        ->assertHasErrors(['email' => $rule]);
})->with([
    'required' => ['value' => '', 'rule' => 'required'],
    'email'    => ['value' => 'invalid-email', 'rule' => 'email'],
]);
