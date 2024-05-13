<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Login extends Component
{
    #[Rule(['required', 'email', 'max:255'])]
    public string $email = '';

    #[Rule(['required', 'max:255'])]
    public string $password = '';

    public function render(): View
    {
        return view('livewire.auth.login');
    }

    public function tryToLogin(): void
    {
        $this->validate();

        if (! auth()->attempt($this->only('email', 'password'))) {
            $this->addError('invalidCredentials', trans('auth.failed'));

            return;
        }

        $this->redirect(route('dashboard'));
    }
}
