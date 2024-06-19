<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class PasswordRecovery extends Component
{
    public ?string $message = null;

    #[Rule(['required', 'email'])]
    public ?string $email = null;

    #[Layout('components.layouts.guest')]
    public function render(): View
    {
        return view('livewire.auth.password-recovery');
    }

    public function requestPasswordRecovery(): void
    {
        $this->validate();

        Password::sendResetLink($this->only('email'));

        $this->message = 'You will receive an email with the link to reset your password.';
    }
}
