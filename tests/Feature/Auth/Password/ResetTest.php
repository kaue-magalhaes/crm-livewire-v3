<?php

use App\Livewire\Auth\PasswordRecovery;
use App\Livewire\Auth\PasswordReset;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function PHPUnit\Framework\assertTrue;

test('need to receive a valid token with a combination with the email', function () {
    Notification::fake();

    $user = User::factory()->create();
    Livewire::test(PasswordRecovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) {
            get(route('password.reset') . '?token=' . $notification->token)
                ->assertSuccessful();

            get(route('password.reset') . '?token=any-token')
                ->assertRedirect(route('login'));

            return true;
        }
    );
});

test('test if is possible to reset the password with the given token', function () {
    Notification::fake();

    $user = User::factory()->create();
    Livewire::test(PasswordRecovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            Livewire::test(PasswordReset::class, ['token' => $notification->token, 'email' => $user->email])
                ->set('email_confirmation', $user->email)
                ->set('password', 'new-password')
                ->set('password_confirmation', 'new-password')
                ->call('updatePassword')
                ->assertHasNoErrors()
                ->assertRedirect(route('login'));

            $user->refresh();

            assertTrue(
                Hash::check('new-password', $user->password)
            );

            return true;
        }
    );
});

test('checking form rules', function ($field) {
    $label = $field->label;
    $value = $field->value;
    $rule  = $field->rule;

    Notification::fake();

    $user = User::factory()->create();
    Livewire::test(PasswordRecovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user, $label, $value, $rule) {
            Livewire::test(PasswordReset::class, ['token' => $notification->token, 'email' => $user->email])
                ->set($label, $value)
                ->call('updatePassword')
                ->assertHasErrors([$label => $rule]);

            return true;
        }
    );
})->with([
    'email:required'     => (object)['label' => 'email', 'value' => '', 'rule' => 'required'],
    'email:confirmed'    => (object)['label' => 'email', 'value' => 'email@email.com', 'rule' => 'confirmed'],
    'email:email'        => (object)['label' => 'email', 'value' => 'not-an-email', 'rule' => 'email'],
    'password:required'  => (object)['label' => 'password', 'value' => '', 'rule' => 'required'],
    'password:confirmed' => (object)['label' => 'password', 'value' => 'any-password', 'rule' => 'confirmed'],
]);

test('need to show an obfuscate email to the user', function () {
    $email           = 'example@test.com';
    $obfuscatedEmail = obfuscate_email($email);

    expect($obfuscatedEmail)
        ->toBe('ex*****@******om');

    // ---

    Notification::fake();

    $user = User::factory()->create();
    Livewire::test(PasswordRecovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            Livewire::test(PasswordReset::class, ['token' => $notification->token, 'email' => $user->email])
                ->assertSet('obfuscatedEmail', obfuscate_email($user->email));

            return true;
        }
    );
});
