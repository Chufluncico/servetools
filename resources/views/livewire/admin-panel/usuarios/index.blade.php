<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;


new class extends Component
{
    use WithPagination;

/*
    public $search = '';
    public $roleFilter = [];
    public string $statusFilter = 'active';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
*/

/* Si se guarda esto en la ruta se guarda tambien para roles y permisos */
    #[Url(except: '')]
    public $search = '';
    #[Url(except: [])]
    public $roleFilter = [];
    #[Url(except: 'active')]
    public string $statusFilter = 'active';
    #[Url]
    public string $sortBy = 'name';
    #[Url]
    public string $sortDirection = 'asc';

    
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
    public function userCounts()
    {
        $counts = User::withTrashed()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN deleted_at IS NULL THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN deleted_at IS NOT NULL THEN 1 ELSE 0 END) as deleted
            ")
            ->first();

        return [
            'total' => $counts->total,
            'active' => $counts->active,
            'deleted' => $counts->deleted,
        ];
    }


    #[Computed]
    public function rolesDisponibles()
    {
        return \Spatie\Permission\Models\Role::orderBy('name')->get();
    }


    #[On('user-saved')]
    public function reRender()
    {
        // método vacío que reacciona al dispatch del form
    }

    /*
    #[On('user-saved')]
    public function refresh()
    {
        $this->resetPage();
    }
    */

    public function getUsuariosPropertyOld()
    {
        $query = User::query()->with('roles');

        //$query->search($this->search);

        if ($this->statusFilter === 'deleted') {
            $query->onlyTrashed();
        }

        if ($this->statusFilter === 'all') {
            $query->withTrashed();
        }

        if (!empty($this->roleFilter)) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', $this->roleFilter);
            });
        }

        return $query
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(5);
    }


    public function getUsuariosProperty()
    {
        return User::query()
            ->with('roles:id,name')
            ->when(
                $this->statusFilter === 'deleted',
                fn ($q) => $q->onlyTrashed()
            )
            ->when(
                $this->statusFilter === 'all',
                fn ($q) => $q->withTrashed()
            )
            /*->when(
                !empty($this->roleFilter),
                fn ($q) => $q->whereHas('roles',
                    fn ($r) => $r->whereIn('name', $this->roleFilter)
                )
            )*/
            ->when(
                !empty($this->roleFilter),
                fn ($q) => $q->role($this->roleFilter)
            )
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(5, pageName:'usersPage');
    }


    /* Hook de $statusFilter */
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }


};
?>


<div>
    

    <div class="p-4 rounded-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
        <div class="flex justify-between items-center mb-2 gap-2">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" clearable placeholder="Buscar (minimo 3 caracteres)" class="md:max-w-1/3" />
            <flux:spacer />
            <flux:button icon="plus" variant="primary" wire:click="$dispatchTo('admin-panel.usuarios.form', 'open-user-create')">Usuario</flux:button>
        </div>    

        <flux:table :paginate="$this->usuarios" pagination:scroll-to wire:key="usuarios-table">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Nombre</flux:table.column>
                <flux:table.column>
                    <flux:dropdown>    
                        <flux:button icon:trailing="funnel" variant="ghost" size="sm" class="ps-0 hover:bg-transparent!" icon-trailing:class="ms-4  hover:text-zinc-800! dark:hover:text-zinc-200! text-[var(--color-accent)]! }}">Estado</flux:button>    
                        <flux:menu>    
                            <flux:radio.group wire:model.live.debounce.300ms="statusFilter" class="p-2">    
                                <flux:field variant="inline">
                                    <flux:radio value="active" />
                                    <flux:label class="place-content-between w-full">Activos<flux:badge size="sm" color="zinc" class="ms-2">{{ $this->userCounts['active'] }}</flux:badge></flux:label>
                                </flux:field>
                                <flux:field variant="inline" class="justify-between w-full">
                                    <flux:radio value="deleted" />
                                    <flux:label class="place-content-between w-full">Desactivados<flux:badge size="sm" color="zinc" class="ms-2">{{ $this->userCounts['deleted'] }}</flux:badge></flux:label>
                                </flux:field>
                                <flux:field variant="inline" class="justify-between w-full">
                                    <flux:radio value="all" />
                                    <flux:label class="place-content-between w-full">Todos<flux:badge size="sm" color="zinc" class="ms-2">{{ $this->userCounts['total'] }}</flux:badge></flux:label>
                                </flux:field>
                            </flux:radio.group>
                        </flux:menu>
                    </flux:dropdown>
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                <flux:table.column>
                    <flux:dropdown>    
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
                    <flux:table.row wire:key="usuario-{{ $usuario->id }}-{{ $usuario->updated_at->timestamp }}">
                        <flux:table.cell>{{ $usuario->name }}</flux:table.cell>
                        <flux:table.cell>
                            @if($usuario->deleted_at)
                                <flux:badge size="sm" color="red">Desactivado</flux:badge>
                            @else
                                <flux:badge size="sm" color="green">Activo</flux:badge>
                            @endif
                        </flux:table.cell>
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
                        <flux:table.cell variant="strong">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                                <flux:menu>
                                    @if($usuario->deleted_at)
                                    <flux:menu.item wire:click="$dispatchTo('admin-panel.usuarios.form', 'confirm-restore-user', { id: {{ $usuario->id }} })">Reactivar</flux:menu.item>
                                    @else        
                                    <flux:menu.item wire:click="$dispatchTo('admin-panel.usuarios.form', 'open-user-edit', { id: {{ $usuario->id }} })">Editar usuario</flux:menu.item>
                                    <flux:menu.item wire:click="$dispatchTo('admin-panel.usuarios.form', 'open-user-password', { id: {{ $usuario->id }} })">Cambiar contraseña</flux:menu.item>           
                                    <flux:menu.separator />        
                                    <flux:menu.item variant="danger" wire:click="$dispatchTo('admin-panel.usuarios.form', 'confirm-delete-user', { id: {{ $usuario->id }} })">Desactivar</flux:menu.item>    
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
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