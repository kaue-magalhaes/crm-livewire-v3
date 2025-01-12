<div>
    <x-modal wire:model="modalOpen" :title="$user?->name" separator>
        @if ($user)
            <div class="spayce-y-2">
                <x-input readonly label="Name" :value="$user->name"/>
                <x-input readonly label="Email" :value="$user->email"/>
                <x-input readonly label="Created At" :value="$user->created_at->format('d/m/Y H:i')"/>
                <x-input readonly label="Updated At" :value="$user->updated_at->format('d/m/Y H:i')"/>
                <x-input readonly label="Deleted At" :value="$user->deleted_at?->format('d/m/Y H:i')"/>
                <x-input readonly label="Deleted By" :value="$user->deletedBy?->name"/> 
            </div>
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.modalOpen = false"/>
        </x-slot:actions>
    </x-modal>
</div>
