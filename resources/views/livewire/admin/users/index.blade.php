<div>
    <x-header title="Users" separator/>

    <div class="mb-4 flex items-center space-x-4">
        <div class="w-1/3">
            <x-input
                icon="o-magnifying-glass"
                label="Search by email and name"
                wire:model.live="search"
            />
        </div>
        <x-choices
            label="Filter by permissions"
            wire:model.live="search_permissions"
            :options="$permissionsToFilter"
            option-label="key"
            search-function="filterPermissions"
            no-result-text="Ops! Nothing here ..."
            searchable
        />
        <x-select
            wire:model.live="perPage"
            :options="$perPageOptions"
            label="Per Page"
        />
        <x-checkbox
            label="Show Deleted Users"
            wire:model.live="search_trash"
            class="checkbox-primary"
            right tight
        />
    </div>

    <x-table :headers="$this->headers" :rows="$this->users" with-pagination>
        @scope('header_id', $header)
        <x-table.th :$header name='id'/>
        @endscope

        @scope('header_name', $header)
        <x-table.th :$header name='name'/>
        @endscope

        @scope('header_email', $header)
        <x-table.th :$header name='email'/>
        @endscope

        @scope('cell_permissions', $user)
        @foreach($user->permissions as $permission)
            <x-badge :value="$permission->key" class="badge-primary"/>
        @endforeach
        @endscope

        @scope('actions', $user)
        @unless($user->trashed())
            <x-button icon="o-trash" wire:click="delete({{ $user->id }})" spinner class="btn-sm btn-ghost"/>
        @else
            <x-button icon="o-arrow-path-rounded-square" wire:click="restore({{ $user->id }})" spinner
                      class="btn-sm btn-ghost"/>
        @endunless
        @endscope
    </x-table>
</div>
