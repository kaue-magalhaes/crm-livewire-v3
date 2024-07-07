@props([
    'header',
    'name', 
])

<div 
    wire:click="sortBy('{{ $name }}', '{{ $header['sortDirection'] == 'asc' ? 'desc' : 'asc' }}')" 
    class="cursor-pointer"
>
    {{$header['label']}}
    @if ($header['sortBy'] === $name)
        <x-icon 
            :name="$header['sortDirection'] == 'asc' ? 'o-chevron-up' : 'o-chevron-down'" 
            class="w-3 h-3 ml-1" 
        />
    @endif
</div>