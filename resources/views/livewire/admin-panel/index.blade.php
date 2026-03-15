<?php

use Livewire\Component;

new class extends Component
{
    
    public string $viewTab = 'usuarios';


    public function setView(string $tab): void
    {
        if (! in_array($tab, ['usuarios', 'roles', 'permisos'])) {
            return;
        }
        $this->viewTab = $tab;
        //$this->reset();
    }


    



};
?>

<div>

    <flux:navbar class="mb-4">
        <flux:navbar.item 
            :current="$viewTab === 'usuarios'"
            wire:click="setView('usuarios')">
            Usuarios
        </flux:navbar.item>
        <flux:navbar.item 
            :current="$viewTab === 'roles'"
            wire:click="setView('roles')">
            Roles
        </flux:navbar.item>
        <flux:navbar.item 
            :current="$viewTab === 'permisos'"
            wire:click="setView('permisos')">
            Permisos
        </flux:navbar.item>
    </flux:navbar>


    @if($viewTab === 'roles')
        <livewire:admin-panel.roles.index :key="'roles-'.$viewTab" />
    @elseif($viewTab === 'permisos')
        <livewire:admin-panel.permisos.index :key="'permisos-'.$viewTab" />
    @else($viewTab === 'usuarios')
        <livewire:admin-panel.usuarios.index :key="'usuarios-'.$viewTab" />
    @endif


</div>