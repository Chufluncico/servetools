<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


new class extends Component
{
    public $show = false;
    public $mode = 'create';
    public ?Role $role = null;
    public $name = '';
    public $selectedPermissions = [];

    protected $listeners = [
        'open-role-modal' => 'create',
        'edit-role' => 'edit',
    ];


    #[Computed]
    public function permissions()
    {
        return Permission::orderBy('name')->get();
    }


    #[Computed]
    public function groupedPermissions()
    {
        return Permission::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {

                return explode('.', $permission->name)[0];

            });
    }


    public function toggleModuleOld($module, $checked)
{
    $permissions = $this->groupedPermissions[$module]
        ->pluck('name')
        ->toArray();

    if ($checked) {

        $this->selectedPermissions = array_values(array_unique(
            array_merge($this->selectedPermissions, $permissions)
        ));

    } else {

        $this->selectedPermissions = array_values(array_diff(
            $this->selectedPermissions,
            $permissions
        ));

    }
}


    public function moduleCheckedOld($module)
{
    $permissions = $this->groupedPermissions[$module]
        ->pluck('name')
        ->toArray();

    return empty(array_diff($permissions, $this->selectedPermissions));
}


    public function create()
    {
        $this->authorize('roles.create');
        $this->reset();
        $this->mode = 'create';
        $this->show = true;
    }


    public function edit($roleId)
    {
        $this->authorize('roles.edit');
        $this->reset();
        $this->role = Role::findOrFail($roleId);
        $this->mode = 'edit';
        $this->name = $this->role->name;
        $this->selectedPermissions = $this->role
            ->permissions
            ->pluck('name')
            ->toArray();
        $this->show = true;
    }


    public function save()
    {
        $this->authorize(
            $this->mode === 'create'
                ? 'roles.create'
                : 'roles.edit'
        );

        $data = $this->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name')->ignore($this->role?->id),
            ],
        ]);

        if ($this->mode === 'create') {
            Role::create($data);
        } else {
            $this->role->update($data);
            $role = $this->role;
        }

        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('role-saved');
        $this->show = false;
        $this->reset();
    }


};
?>

<div>


    <flux:modal wire:model="show" class="xmd:w-96">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $mode === 'create' ? 'Crear rol' : 'Editar rol' }}</flux:heading>
            <flux:input label="Nombre del rol" wire:model="name" />
            <div class="space-y-2">
                <flux:heading size="sm">
                    Permisos
                </flux:heading>
                @foreach($this->groupedPermissions as $module => $permissions)
                    <div class="border rounded-md p-3 space-y-4">
                        <flux:checkbox.group wire:key="module-{{ $module }}">
                            <flux:field class="flex!">   
                                <flux:label class="">{{ Str::headline($module) }}</flux:label>    
                                <flux:checkbox.all 
                                />    
                            </flux:field>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($permissions as $permission)
                                    @php
                                        $action = explode('.', $permission->name)[1];
                                    @endphp
                                    <flux:checkbox
                                        wire:model="selectedPermissions"
                                        value="{{ $permission->name }}"
                                        label="{{ $action }}"
                                    />
                                @endforeach
                            </div>
                        </flux:checkbox.group>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="ghost" wire:click="$set('show', false)">Cancelar</flux:button>
                <flux:button variant="primary" wire:click="save">Guardar</flux:button>
            </div>
        </div>
    </flux:modal>


</div>