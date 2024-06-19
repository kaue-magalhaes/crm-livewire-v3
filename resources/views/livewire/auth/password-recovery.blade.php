<div>
    <x-card title="Password recovery" shadow class="mx-auto w-[450px]">

        @if($message)
            <x-alert icon="o-exclamation-triangle" class="alert-success mb-2">
                <span>{{ $message }}</span>
            </x-alert>
        @endif

        <x-form wire:submit="requestPasswordRecovery">
            <x-input label="Email" wire:model="email"/>

            <x-slot:actions>
                <div class="w-full flex items-center justify-between">
                    <a wire:navigate href="{{ route('login') }}" class="link">
                        I remember my password now
                    </a>
                    <div>
                        <x-button label="Send email" class="btn-primary" type="submit" spinner="submit"/>
                    </div>
                </div>
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
