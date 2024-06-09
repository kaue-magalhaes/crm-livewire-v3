<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Login extends Component
{
    #[Rule(['required', 'email', 'max:255'])]
    public string $email = '';

    #[Rule(['required', 'max:255'])]
    public string $password = '';

    #[Layout('components.layouts.guest')]
    public function render(): View
    {
        return view('livewire.auth.login');
    }

    public function tryToLogin(): void
    {
        if ($this->ensureIsNotRateLimited()) {
            return;
        }

        $this->validate();

        if (! auth()->attempt($this->only('email', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            $this->addError('invalidCredentials', trans('auth.failed'));

            return;
        }

        $this->redirect(route('dashboard'));
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }

    private function ensureIsNotRateLimited(): bool
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $this->addError('rateLimiter', trans('auth.throttle', [
                'seconds' => RateLimiter::availableIn($this->throttleKey()),
            ]));

            return true;
        }

        return false;
    }
}
