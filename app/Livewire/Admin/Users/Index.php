<?php

namespace App\Livewire\Admin\Users;

use App\Enums\Can;
use App\Models\Permission;
use App\Models\User;
use App\Support\Table\Header;
use App\Traits\Livewire\HasTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read Collection|User[] $users
 * @property-read array $headers
 */
class Index extends Component
{
    use HasTable;
    use WithPagination;

    #[Rule('exists:permissions,id')]
    public array $search_permissions = [];

    public bool $search_trash = false;

    public Collection $permissionsToFilter;

    public array $perPageOptions = [
        ['id' => 5, 'name' => 5],
        ['id' => 15, 'name' => 15],
        ['id' => 25, 'name' => 25],
        ['id' => 50, 'name' => 50],
    ];

    public function mount(): void
    {
        $this->authorize(Can::BE_AN_ADMIN->value);
        $this->filterPermissions();
    }

    #[On(['user::deleted', 'user::restored'])]
    public function render(): View
    {
        return view('livewire.admin.users.index');
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function query(): Builder
    {
        return User::query()
            ->with('permissions')
            ->when(
                $this->search_permissions,
                fn (Builder $q) => $q
                    ->whereHas(
                        'permissions',
                        fn (Builder $q) => $q
                            ->whereIn('id', $this->search_permissions)
                    )
            )
            ->when(
                $this->search_trash,
                fn (Builder $q) => $q->onlyTrashed()
            );
    }

    public function searchColumns(): array
    {
        return ['name', 'email'];
    }

    public function tableHeaders(): array
    {
        return [
            Header::make('id', '#'),
            Header::make('name', 'Name'),
            Header::make('email', 'Email'),
            Header::make('permissions', 'Permissions'),
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

    public function destroy(int $id): void
    {
        $this->dispatch('user::deletion', userId: $id)->to('admin.users.delete');
    }

    public function impersonate(int $id): void
    {
        $this->dispatch('user::impersonation', userId: $id)->to('admin.users.impersonate');
    }

    public function restore(int $id): void
    {
        $this->dispatch('user::restoring', userId: $id)->to('admin.users.restore');
    }

    public function showUser(int $id): void
    {
        $this->dispatch('user::show', id: $id)->to('admin.users.show');
    }
}
