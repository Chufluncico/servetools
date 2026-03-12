<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;


new class extends Component
{

    use WithPagination;

    
    #[Computed]
    public function permisos()
    {
        return Permission::with('roles')
            ->orderBy('name')
            ->paginate(5);
    }






};
?>

<div>


    <flux:table :paginate="$this->permisos">
        <flux:table.columns>
            <flux:table.column sortable>Nombre</flux:table.column>
            <flux:table.column sortable sorted direction="desc">Rol</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($this->permisos as $permiso)
                <flux:table.row>
                    <flux:table.cell>{{ $permiso->name }}</flux:table.cell>
                    <flux:table.cell>{{ $permiso->roles->pluck('name')->join(', ') }}</flux:table.cell>
                    <flux:table.cell variant="strong"><flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button></flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell>No hay roles definidos</flux:table.cell>
                </flux:table.row>
            @endforelse
           
        </flux:table.rows>
    </flux:table>

    
</div>


