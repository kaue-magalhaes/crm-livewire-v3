<div>
    <x-card title="Login" shadow class="mx-auto w-[450px]">

        @if($errors->hasAny(['invalidCredentials', 'rateLimiter']))
            <x-alert icon="o-exclamation-triangle" class="alert-warning">
                @error('invalidCredentials')
                <span>{{ $message  }}</span>
                @enderror

                @error('rateLimiter')
                <span>{{ $message  }}</span>
                @enderror
            </x-alert>
        @endif

        <x-form wire:submit="tryToLogin">
            <x-input label="Email" wire:model="email"/>
            <x-input label="Password" wire:model="password" type="password"/>
            <div class="w-full text-right">
                <a wire:navigate href="{{ route('password.recovery') }}" class="link link-hover">
                    Forgot password?
                </a>
            </div>

            <x-slot:actions>
                <div class="w-full flex items-center justify-between">
                    <a wire:navigate href="{{ route('register') }}" class="link link-primary">
                        I want to create an account
                    </a>
                    <div>
                        <x-button label="Reset" type="reset"/>
                        <x-button label="Login" class="btn-primary" type="submit" spinner="submit"/>
                    </div>
                </div>
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
