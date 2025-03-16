<?php

use App\Livewire\Admin;
use App\Models\User;
use App\Notifications\UserRestoredAccessNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;

it('should be able to restore a user', function () {
    $user           = User::factory()->admin()->create();
    $forRestoration = User::factory()->deleted()->create();

    actingAs($user);
    Livewire::test(Admin\Users\Restore::class)
        ->set('user', $forRestoration)
        ->set('confirmation_confirmation', 'YODA')
        ->call('restore')
        ->assertDispatched('user::restored');

    assertNotSoftDeleted('users', [
        'id' => $forRestoration->id,
    ]);

    $forRestoration->refresh();
    expect($forRestoration)->restored_at->not->toBeNull()
        ->restoredBy->id->toBe($user->id);
});

it('should have a confirmation before restaration', function () {
    $user           = User::factory()->admin()->create();
    $forRestoration = User::factory()->deleted()->create();

    actingAs($user);
    Livewire::test(Admin\Users\Restore::class)
        ->set('user', $forRestoration)
        ->call('restore')
        ->assertHasErrors(['confirmation' => 'confirmed'])
        ->assertNotDispatched('user::restored');

    assertSoftDeleted('users', [
        'id' => $forRestoration->id,
    ]);
});

it('should send a notification to the user informing them that their account has been restored', function () {
    Notification::fake();

    $user           = User::factory()->admin()->create();
    $forRestoration = User::factory()->deleted()->create();

    actingAs($user);
    Livewire::test(Admin\Users\Restore::class)
        ->set('user', $forRestoration)
        ->set('confirmation_confirmation', 'YODA')
        ->call('restore')
        ->assertMethodWired('restore');

    Notification::assertSentTo($forRestoration, UserRestoredAccessNotification::class);
});

it('should be possible to find a deleted user before restoring their access', function () {
    $user           = User::factory()->admin()->create();
    $forRestoration = User::factory()->deleted()->create();

    actingAs($user);
    Livewire::test(Admin\Users\Restore::class)
        ->set('confirmation_confirmation', 'YODA')
        ->call('openRestoreConfirmationFor', $forRestoration->id)
        ->assertSet('user', function ($user) use ($forRestoration) {
            expect($user->id)->toBe($forRestoration->id)
                ->and($user->name)->toBe($forRestoration->name);

            return true;
        });

    assertSoftDeleted('users', [
        'id' => $forRestoration->id,
    ]);
});

test('check if component is in the page', function () {
    actingAs(User::factory()->admin()->create());
    Livewire::test(Admin\Users\Index::class)
        ->assertContainsLivewireComponent('admin.users.restore');
});
