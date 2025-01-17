<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Notifications\UserRestoredAccessNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Restore extends Component
{
    use Toast;

    public ?User $user = null;

    public bool $showConfirmationModal = false;

    #[Rule(['required', 'confirmed'])]
    public string $confirmation = 'YODA';

    public ?string $confirmation_confirmation = null;

    public function render(): View
    {
        return view('livewire.admin.users.restore');
    }

    #[On('user::restoring')]
    public function openRestoreConfirmationFor(int $userId): void
    {
        $this->user                  = User::select('id', 'name')->withTrashed()->find($userId);
        $this->showConfirmationModal = true;
    }

    public function restore(): void
    {
        $this->validate();

        if ($this->user->is(auth()->user())) {
            $this->addError('confirmation', "You can't restore yourself brow.");

            return;
        }

        $this->user->restore();
        $this->user->restored_at = now();
        $this->user->restored_by = auth()->id();
        $this->user->save();

        $this->reset('showConfirmationModal');

        $this->success('The user has been successfully restored!');

        $this->user->notify(new UserRestoredAccessNotification);

        $this->dispatch('user::restored');
    }
}
