<?php

use App\Models\Modalidad;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;


new class extends Component {

    public bool $showModal = false;
    public string $aet = '';
    public string $ip = '';


    #[On('create-modality')]
    public function openCreateModal()
    {
        $this->authorize('create', Modalidad::class);
        $this->showModal = true;
    }

    public function create(): void
    {
        $this->authorize('create', Modalidad::class);
        $validated = $this->validate([
            'aet' => ['required', 'string', 'max:255', 'unique:modalidades,aet'],
            'ip' => ['ip', 'unique:modalidades,ip'],
        ]);
        $modality = Modalidad::create([
            'aet' => $validated['aet'],
        ]);
        $this->dispatch('modality-created', aet: $modality->aet);
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['aet']);
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }


};
?>

<flux:modal wire:model.self="showModal" :dismissible="false" :closable="false">
    <div class="space-y-6">
        <flux:heading size="lg">{{ __('rx.new_modality') }}</flux:heading>

        <flux:input wire:model="aet" label="{{ __('AE Title') }}" badge="Required"/>

        <flux:input 
            wire:model="ip"
            icon="rectangle-ellipsis"
            label="{{ __('rx.ip_address') }}"
            placeholder="192.168.1.1"
            pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}" 
            x-mask:dynamic="[ ...$input.split('.').map( (x, index, array) => '9'.repeat( x.length > 0 && x.length < 3 ? x.length + ( index === array.length -1 ? 1 : 0 ) : 3 ) ),'999','999','999','999'].splice(0,4).join('.')"
        />

        <div class="flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" wire:click="create" wire:loading.attr="disabled">
                {{ __('Save') }}
            </flux:button>
        </div>
    </div>
</flux:modal>