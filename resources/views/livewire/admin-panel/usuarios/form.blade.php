<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;
use App\Concerns\ProfileValidationRules;
use App\Concerns\PasswordValidationRules;
//use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
//use Illuminate\Validation\ValidationException;
//use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\User\UpdateUserProfile;
use App\Actions\User\UpdateUserPassword;
use App\Actions\User\DeleteUser;


new class extends Component
{
    use AuthorizesRequests, ProfileValidationRules, PasswordValidationRules;

    public $show = false;
    public $mode = 'create'; // create | edit
    public ?User $user = null;
    //public $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $roles = [];

    public bool $confirming = false;
    public string $confirmAction = '';
    public string $confirmMessage = '';
    public ?User $confirmUser = null;


    #[On('open-user-create')]
    public function openCreate()
    {
        //$this->authorize('users.create');

        $this->reset();
        $this->mode = 'create';
        $this->show = true;
    }


    #[On('open-user-edit')]
    public function openEdit($id)
    {
        //$this->authorize('users.edit');
        $this->reset();

        $this->user = User::with('roles')->findOrFail($id);
        //$this->user = $user;
        $this->mode = 'edit';
        //$this->userId = $user->id;

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->roles = $this->user->roles->pluck('name')->toArray();

        $this->show = true;
    }


    #[On('open-user-password')]
    public function openPassword($id)
    {
        //$this->authorize('users.edit');
        $this->reset();
        $this->user = User::findOrFail($id);
        //$this->user = $user;
        $this->mode = 'changePassword';
        //$this->userId = $user->id;

        $this->show = true;
    }


    #[On('confirm-delete-user')]
    public function confirmDeleteUser($id)
    {
        $this->confirmUser = User::findOrFail($id);
        $this->confirmAction = 'delete';
        $this->confirmMessage ="¿Seguro que deseas eliminar el usuario {$this->confirmUser->name}?";
        $this->confirming = true;
    }


    #[On('confirm-restore-user')]
    public function confirmRestoreUser($id)
    {
        $this->confirmUser = User::withTrashed()->findOrFail($id);

        $this->confirmAction = 'restore';

        $this->confirmMessage =
            "¿Deseas reactivar el usuario {$this->confirmUser->name}?";

        $this->confirming = true;
    }


    #[Computed]
    public function isLastSuperadmin()
    {
        if (!$this->user) {
            return false;
        }

        if (!$this->user->hasRole('superadmin')) {
            return false;
        }

        return User::role('superadmin')
            ->withoutTrashed()
            ->count() === 1;
    }


    public function save()
    {
        /*
        if ($this->mode === 'create') {
            $this->createUser();
        } else {
            $this->updateUser();
        }
        */
        match ($this->mode) {
            'create' => $this->createUser(),
            'edit' => $this->updateUser(),
            'changePassword' => $this->updatePassword(),
        };
    }


    protected function createUser()
    {
        //$this->authorize('users.create');

        $user = app(CreatesNewUsers::class)->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $user->syncRoles($this->roles);

        $this->dispatch('alert', type: 'success', message: 'Usuario creado.');
        $this->dispatch('user-saved');
        $this->show = false;
    }


    protected function updateUser2()
    {
        //$this->authorize('users.edit');
        $user = User::findOrFail($this->userId);
        $rules = $this->profileRules($user->id);

    /*
        if (!empty($this->password)) {
            $rules['password'] = $this->passwordRules();
        }
    */
        $validated = $this->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
    /*
        if (!empty($this->password)) {
            $data['password'] = $this->password;
        }
    */
        $user->update($data);
        $user->syncRoles($this->roles);
        $this->dispatch('alert', type: 'success', message: 'Usuario actualizado.');
        $this->dispatch('user-saved');
        //$this->dispatch('$refresh')->to('admin-panel.usuarios.index');
        $this->show = false;
    }


    public function updateUser()
{
    //$user = User::findOrFail($this->userId);
    //$user = $this->user;

    $validated = $this->validate(
        $this->profileRules($this->user->id)
    );

    $validated['roles'] = $this->roles;

    app(UpdateUserProfile::class)->update($this->user, $validated);
    
    $this->dispatch('alert', type: 'success', message: 'Usuario actualizado.');
    $this->dispatch('user-saved');
    $this->show = false;
}


    public function updatePassword2()
    {
        //$this->authorize('users.edit');

        $user = User::findOrFail($this->userId);

        $validated = $this->validate([
            'password' => $this->passwordRules(),
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['password', 'password_confirmation']);
        $this->dispatch('alert', type: 'success', message: 'Contraseña actualizada.');
        $this->show = false;
    }


    public function updatePassword()
    {
        //$user = User::findOrFail($this->userId);
        //$user = $this->user;

        app(UpdateUserPassword::class)->update(
            $this->user,
            [
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]
        );

        $this->reset(['password', 'password_confirmation']);
        $this->dispatch('alert', type: 'success', message: 'Contraseña actualizada.');
        $this->show = false;
    }


    #[Computed]
    public function rolesDisponibles()
    {
        return Role::orderBy('name')->get();
    }


    public function deleteUserOld()
    {
        app(DeleteUser::class)->delete($this->user);

        $this->dispatch('alert',
            type: 'success',
            message: 'Usuario eliminado.'
        );

        $this->dispatch('user-saved');
        $this->show = false;
    }


    public function confirmDeleteOld()
    {
        $this->confirmAction = 'delete';
        $this->confirmMessage = "¿Seguro que deseas eliminar el usuario {$this->user->name}?";

        $this->confirming = true;
    }


    public function confirmRestoreOld()
    {
        $this->confirmAction = 'restore';
        $this->confirmMessage = "¿Deseas reactivar el usuario {$this->user->name}?";

        $this->confirming = true;
    }


    public function executeConfirmed()
    {
        match ($this->confirmAction) {
            'delete' => app(DeleteUser::class)->delete($this->confirmUser),
            'restore' => $this->confirmUser->restore(),
        };

        $this->dispatch('alert',
            type: 'success',
            message: $this->confirmAction === 'delete'
                ? 'Usuario eliminado.'
                : 'Usuario reactivado.'
        );

        $this->dispatch('user-saved');
        $this->confirming = false;
    }


};
?>


<div>


    <flux:modal wire:model="show" class="w-2xl">
        <flux:heading size="lg">
            @switch($mode)
                @case('create') Nuevo Usuario @break
                @case('edit') Editar Usuario @break
                @case('changePassword') Cambiar Contraseña @break
            @endswitch
        </flux:heading>

        <div class="mt-6 space-y-4">
            @if($mode === 'create')
                <flux:input label="Nombre" wire:model.defer="name" />
                <flux:input label="Email" type="email" wire:model.defer="email" />
                <flux:input label="Contraseña" type="password" wire:model.defer="password" />
                <flux:input label="Confirmar contraseña" type="password" wire:model.defer="password_confirmation" />
                <flux:checkbox.group wire:model.defer="roles">
                    @foreach($this->rolesDisponibles as $role)
                        {{-- Si el rol es superadmin y el usuario actual NO es superadmin, no se muestra --}}
                        @if($role->name === 'superadmin' && !auth()->user()->hasRole('superadmin'))
                            @continue
                        @endif
                        <flux:field variant="inline">
                            <flux:checkbox value="{{ $role->name }}" />
                            <flux:label>
                                {{ $role->name }}
                            </flux:label>
                        </flux:field>
                    @endforeach
                </flux:checkbox.group>
            @endif

            @if($mode === 'edit')
                <flux:input label="Nombre" wire:model.defer="name" />
                <flux:input label="Email" type="email" wire:model.defer="email" />
                <flux:checkbox.group wire:model.defer="roles">
                    @foreach($this->rolesDisponibles as $role)
                        {{-- Si el rol es superadmin y el usuario actual NO es superadmin, no se muestra --}}
                        @if($role->name === 'superadmin' && !auth()->user()->hasRole('superadmin'))
                            @continue
                        @endif
                        @php
                            $disable =
                                $role->name === 'superadmin' &&
                                $this->user &&
                                $this->user->id === auth()->id();
                        @endphp
                        <flux:field variant="inline">
                            <flux:checkbox value="{{ $role->name }}" :disabled="$disable" />
                            <flux:label>{{ $role->name }}
                            </flux:label>
                        </flux:field>
                    @endforeach
                </flux:checkbox.group>
            @endif

            @if($mode === 'changePassword')
                <flux:field>    
                    <flux:label>Contraseña</flux:label>    
                    <flux:input type="password" wire:model.defer="password" />    
                    <flux:description class="mt-0!">{{ __('custom.password_requirements') }}</flux:description>
                    <flux:error name="password" class="mt-0!" />    
                </flux:field>
                <flux:field>    
                    <flux:label>Repite la contraseña</flux:label>    
                    <flux:input type="password" wire:model.defer="password_confirmation" />    
                    <flux:error name="password_confirmation" class="mt-0!" />    
                </flux:field>
            @endif
        </div>

        <div class="flex gap-2 mt-4">
            <flux:spacer />
            <flux:button variant="ghost" wire:click="$set('show', false)">
                Cancelar
            </flux:button>
            <flux:button variant="primary" wire:click="save">
                @switch($mode)
                    @case('create') Crear Usuario @break
                    @case('edit') Actualizar Usuario @break
                    @case('changePassword') Cambiar Contraseña @break
                @endswitch
            </flux:button>
        </div>
    </flux:modal>


    <flux:modal wire:model="confirming" class="w-md">
        <div class="mt-4">{{ $confirmMessage }}</div>
        <div class="flex gap-2 mt-6">
            <flux:spacer />
            <flux:button variant="ghost" wire:click="$set('confirming', false)">Cancelar</flux:button>
            <flux:button variant="danger" wire:click="executeConfirmed">Confirmar</flux:button>
        </div>
    </flux:modal>



</div>