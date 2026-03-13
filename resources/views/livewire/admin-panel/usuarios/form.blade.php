<?php

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;


new class extends Component
{
    
    public $show = false;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $roles = [];
    public $active = true;

    #[On('open-user-form')]
    public function open()
    {
        $this->reset();
        $this->active = true;
        $this->show = true;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => 'array',
            'active' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password, // cast hashed
            'active' => $this->active,
        ]);

        $user->syncRoles($this->roles);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->dispatch('alert', type: 'success', message: 'Usuario creado correctamente.');
        $this->dispatch('user-created');

        $this->show = false;
    }

    #[\Livewire\Attributes\Computed]
    public function rolesDisponibles()
    {
        return Role::orderBy('name')->get();
    }




};
?>




<div>


    <flux:modal wire:model="show" class="w-2xl">
        <flux:heading size="lg">
            Nuevo Usuario
        </flux:heading>

        <div class="mt-6 space-y-4">
            <flux:input label="Nombre" wire:model.defer="name" />
            <flux:input label="Email" type="email" wire:model.defer="email" />
            <flux:input label="Contraseña" type="password" wire:model.defer="password" />
            <flux:input label="Confirmar contraseña" type="password" wire:model.defer="password_confirmation" />
            <flux:checkbox.group wire:model.defer="roles">
                @foreach($this->rolesDisponibles as $role)
                    <flux:field variant="inline">
                        <flux:checkbox value="{{ $role->name }}" />
                        <flux:label>{{ $role->name }}</flux:label>
                    </flux:field>
                @endforeach
            </flux:checkbox.group>
            <flux:field>
                <flux:checkbox wire:model.defer="active" />
                <flux:label>Usuario activo</flux:label>
            </flux:field>
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:button variant="ghost" wire:click="$set('show', false)">
                Cancelar
            </flux:button>
            <flux:button variant="primary" wire:click="save">
                Crear Usuario
            </flux:button>
        </div>
    </flux:modal>



</div>