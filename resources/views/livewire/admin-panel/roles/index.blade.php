<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;

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
    public function roles()
    {
        return Role::query()
            ->withCount(['users', 'permissions'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }





};
?>



<div>
    

    <div class="p-4 rounded-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
        <flux:table :paginate="$this->roles">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')" >Nombre</flux:table.column>
                <flux:table.column>Usuarios</flux:table.column>
                <flux:table.column>Permisos</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($this->roles as $rol)
                    <flux:table.row>
                        <flux:table.cell>{{ $rol->name }}</flux:table.cell>
                        <flux:table.cell><flux:badge size="sm" color="zinc">{{ $rol->users_count }}</flux:badge></flux:table.cell>
                        <flux:table.cell><flux:badge size="sm" color="zinc">{{ $rol->permissions_count }}</flux:badge></flux:table.cell>
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


</div>