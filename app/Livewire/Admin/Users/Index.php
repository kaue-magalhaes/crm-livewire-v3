<?php

namespace App\Livewire\Admin\Users;

use App\Enums\Can;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read LengthAwarePaginator|User[] $users
 */
class Index extends Component
{
    use WithPagination;

    public ?string $search = null;

    #[Rule('exists:permissions,id')]
    public array $search_permissions = [];

    public Collection $permissionsToFilter;

    public function mount(): void
    {
        $this->authorize(Can::BE_AN_ADMIN->value);
        $this->filterPermissions();
    }

    public function render(): View
    {
        return view('livewire.admin.users.index');
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when(
                $this->search,
                fn (Builder $q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->when(
                $this->search_permissions,
                fn (Builder $q) => $q
                    ->whereHas(
                        'permissions',
                        fn (Builder $q) => $q
                            ->whereIn('id', $this->search_permissions)
                    )
            )
            ->paginate(10);
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'permissions', 'label' => 'Permissions'],
        ];
    }

    public function filterPermissions(?string $key = null): void
    {
        $this->permissionsToFilter = Permission::query()
            ->when(
                $key,
                fn (Builder $q) => $q
                    ->where('key', 'like', "%{$key}%")
            )
            ->orderBy('key')
            ->get();
    }
}
