<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;


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
    public function usuarios2()
    {
        return User::query()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(5);
    }

    #[Computed]
    public function usuarios()
    {
        $query = User::query();

        if (strlen($this->search) >= 3) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);
        return $query->paginate(5);
    }



};
?>


<div>
    

    <div class="p-4 rounded-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
        <div class="flex justify-between items-center mb-2">
            <flux:input wire.model.live icon="magnifying-glass" placeholder="" class="md:max-w-1/3" />
            <flux:spacer />
            <flux:button icon="plus" variant="primary">Usuario</flux:button>
        </div>    

        <flux:table :paginate="$this->usuarios" pagination:scroll-to>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Nombre</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($this->usuarios as $usuario)
                    <flux:table.row>
                        <flux:table.cell>{{ $usuario->name }}</flux:table.cell>
                        <flux:table.cell>{{ $usuario->email }}</flux:table.cell>
                        <flux:table.cell variant="strong"><flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button></flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell>No hay usuarios</flux:table.cell>
                    </flux:table.row>
                @endforelse
               
            </flux:table.rows>
        </flux:table>
    </div>


</div>