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

    public function removeFile()
    {
        $this->reset('file');
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

};
?>

<div>
    {{-- Botón abrir modal --}}
    <flux:button variant="outline" wire:click="open">
        Importar
    </flux:button>

    {{-- Modal --}}
    <flux:modal
        wire:model="showModal"
        :dismissible="false"
        :closable="false"
    >
        <div class="space-y-6">
            <flux:heading size="lg">
                Importar modalidades
            </flux:heading>

            @if (!$file)
                <div
                    x-data="{ uploading: false, progress: 0 }"
                    x-init="
                        $watch('$wire.file', value => {
                            if (!value) {
                                progress = 0;
                                uploading = false;
                            }
                        })
                    "
                    x-on:livewire-upload-start="uploading = true; progress = 0"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                >
                    <!-- File Input -->
                    <flux:input type="file" wire:model="file" />

                    <!-- Progress Bar -->
                    <div x-show="uploading || progress === 100" class="mt-3">
                        <div class="w-full bg-gray-200 rounded h-2 overflow-hidden">
                            <div class="bg-blue-500 h-2 transition-all duration-200" :style="'width: ' + progress + '%'"
                            ></div>
                        </div>

                        <div class="text-sm text-gray-500 mt-1">
                            <span x-show="progress < 100">
                                Subiendo archivo... <span x-text="progress + '%'"></span>
                            </span>

                            <span x-show="progress === 100">
                                Archivo cargado correctamente ✓
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <flux:card size="sm" class="flex items-center py-2">
                    <flux:heading class="mr-4">
                        {{ $file->getClientOriginalName() }} 
                        <flux:text>{{ number_format($file->getSize() / 1024, 2) }} KB</flux:text>
                    </flux:heading>
                    <flux:button wire:click="removeFile" icon="x-mark" variant="subtle" class="ml-auto text-zinc-400" />
                </flux:card>
            @endif

            {{-- Indicador importación --}}
            <div
                wire:loading.flex
                wire:target="import"
                class="items-center gap-2 text-sm text-gray-500"
            >
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                    </path>
                </svg>
                Importando datos...
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="close">
                    Cancelar
                </flux:button>

                <flux:button
                    variant="primary"
                    wire:click="import"
                    wire:loading.attr="disabled"
                    wire:target="file, import"
                    :disabled="!$file"
                >
                    Importar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>