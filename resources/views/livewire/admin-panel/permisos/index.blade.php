<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;


new class extends Component
{

    use WithPagination;

    public $sortBy = 'name';
    public $sortDirection = 'asc';


    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    
    #[Computed]
    public function permisos()
    {
        return Permission::with('roles')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(5);
    }






};
?>

<div>


    <div class="p-4 rounded-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
        <flux:table :paginate="$this->permisos">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Nombre</flux:table.column>
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
                        <flux:table.cell>No hay permisos definidos</flux:table.cell>
                    </flux:table.row>
                @endforelse
               
            </flux:table.rows>
        </flux:table>
    </div>

    
</div>


