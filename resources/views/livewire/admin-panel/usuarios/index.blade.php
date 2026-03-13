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
    public $search = '';
    public $roleFilter = [];

    
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
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(5);
    }


    #[Computed]
    public function rolesDisponibles()
    {
        return \Spatie\Permission\Models\Role::orderBy('name')->get();
    }


    #[Computed]
    public function usuarios3()
    {
        $query = User::query()->with('roles');

        if (strlen($this->search) >= 3) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        // 🎭 Filtro multi-rol
        if (!empty($this->roleFilter)) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', $this->roleFilter);
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(5);
    }


    #[Computed]
    public function usuarios()
    {
        $query = User::query()
            ->with('roles');

        if (strlen($this->search) >= 3) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->roleFilter)) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', $this->roleFilter);
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
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar" class="md:max-w-1/3" />
            <flux:spacer />
            <flux:button icon="plus" variant="primary" wire:click="$dispatch('open-user-form')">Usuario</flux:button>
        </div>    

        <flux:table :paginate="$this->usuarios" pagination:scroll-to>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Nombre</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                <flux:table.column>
                    <flux:dropdown class="w-fulll" >    
                        <flux:button icon:trailing="funnel" variant="ghost" size="sm" class="ps-0 hover:bg-transparent!" icon-trailing:class="ms-4  hover:text-zinc-800! dark:hover:text-zinc-200! {{ !empty($roleFilter) ? 'text-[var(--color-accent)]!' : 'text-zinc-400!' }}">Roles</flux:button>    
                        <flux:menu keep-open>    
                            <flux:checkbox.group wire:model.live="roleFilter" class="p-2">    
                            @foreach($this->rolesDisponibles as $role)
                                <flux:field variant="inline">    
                                    <flux:checkbox  value="{{ $role->name }}" />    
                                    <flux:label>{{ $role->name }}</flux:label>    
                                </flux:field>
                            @endforeach    
                            </flux:checkbox.group>
                        </flux:menu>
                    </flux:dropdown>
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Creado</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">Modificado</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($this->usuarios as $usuario)
                    <flux:table.row>
                        <flux:table.cell>{{ $usuario->name }}</flux:table.cell>
                        <flux:table.cell>{{ $usuario->email }}</flux:table.cell>
                        <flux:table.cell>
                            @forelse($usuario->roles as $role)
                                <flux:badge
                                    size="sm"
                                    color="zinc"
                                    class="me-1"
                                >
                                    {{ $role->name }}
                                </flux:badge>
                            @empty
                                <span class="text-xs text-zinc-400">
                                    Sin rol
                                </span>
                            @endforelse
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $usuario->created_at->format('d/m/Y - H:i') }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $usuario->updated_at->format('d/m/Y - H:i') }}</flux:table.cell>
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


    <livewire:admin-panel.usuarios.form />


</div>