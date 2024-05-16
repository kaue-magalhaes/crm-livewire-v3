<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Notifications\PasswordRecoveryNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

class PasswordRecovery extends Component
{
    public ?string $message = null;

    #[Rule(['required', 'email'])]
    public ?string $email = null;

    public function render(): View
    {
        return view('livewire.auth.password-recovery');
    }

    public function requestPasswordRecovery(): void
    {
        $this->validate();
        $user = User::query()->where('email', $this->email)->first();

        $user?->notify(new PasswordRecoveryNotification());

        $this->message = 'You will receive an email with the link to reset your password.';
    }
}
