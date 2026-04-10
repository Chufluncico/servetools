<?php

use App\Models\Modalidad;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;


new class extends Component {

    public bool $showModal = false;
    public string $name = '';


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
            'name' => ['required', 'string', 'max:255', 'unique:modalidades,name'],
            'ip' => ['ip', 'unique:modalidades,ip'],
        ]);
        $modality = Modalidad::create([
            'name' => $validated['name'],
        ]);
        $this->dispatch('modality-created', name: $modality->name);
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['name']);
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }


};
?>

<flux:modal wire:model.self="showModal" :dismissible="false" :closable="false" flyout position="right">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ __('rx.new_modality') }}
        </flux:heading>

        <flux:input wire:model="name" label="{{ __('rx.modality_name') }}" />

        <flux:field>
            <flux:label>{{ __('rx.ip_address') }}</flux:label>

            <flux:input 
                wire:model="ip" 
                pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}" 
                x-mask:dynamic="[ ...$input.split('.').map( (x, index, array) => '9'.repeat( x.length > 0 && x.length < 3 ? x.length + ( index === array.length -1 ? 1 : 0 ) : 3 ) ),'999','999','999','999'].splice(0,4).join('.')"
            />

            <flux:error name="ip" />
        </flux:field>

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