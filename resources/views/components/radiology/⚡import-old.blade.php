<?php

use App\Imports\ModalidadesImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;


new class extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    public $file;


    public function open()
    {
        $this->reset('file');
        $this->showModal = true;
    }

    public function close()
    {
        $this->reset('file');
        $this->showModal = false;
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx',
        ]);

        Excel::import(new ModalidadesImport, $this->file);

        $this->dispatch('alert',
            type: 'success',
            message: 'Importación completada.'
        );

        $this->dispatch('modalities-imported');

        $this->close();
    }


    public function importOld()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx',
        ]);

        Excel::import(new ModalidadesImport, $this->file);

        $this->dispatch('alert', 
            type: 'success', 
            message: 'Importación completada correctamente.'
        );

        $this->reset('file');
    }
};
?>

<div>

    {{-- Botón que abre modal --}}
    <flux:button variant="outline" wire:click="open">
        Importar
    </flux:button>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" :dismissible="false" :closable="false">
        <div class="space-y-6">
            <flux:heading size="lg">Importar modalidades</flux:heading>

            <flux:input type="file" wire:model="file" label="Seleccionar archivo" />



            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button
                    variant="primary"
                    wire:click="import"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="file">Procesar</span>
                    <span wire:loading wire:target="file">Importando...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>