<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
//use Spatie\Permission\Models\Permission;
use App\Models\Modalidad;


new class extends Component
{

    use WithPagination;

    





};
?>

<div>


    <div class="flex items-center gap-4">
        <flux:heading level="1" size="xl">Inventario Modalidades</flux:heading>
        {{-- <flux:spacer /> --}}
        <flux:modal.trigger name="form-modalidad">    
            <flux:button variant="primary">Nueva Modalidad</flux:button>
        </flux:modal.trigger>
        <livewire:radiologia.modalidades.form />
    </div>


    
</div>


