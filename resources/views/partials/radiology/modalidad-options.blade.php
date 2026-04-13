<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

    <flux:menu>
        @can('update', $modalidad)
            <flux:menu.item wire:click="$dispatch('edit-modalidad', { modalidadId: {{ $modalidad->id }} })">
                {{ __('Editar') }}
            </flux:menu.item>
        @endcan
        @can('delete', $modalidad)
            <flux:menu.item 
                wire:click="$dispatch('delete-modalidad', { modalidadId: {{ $modalidad->id }} })"
                variant="danger"
            >
                {{ __('Delete') }}
            </flux:menu.item>
        @endcan
    </flux:menu>
</flux:dropdown>