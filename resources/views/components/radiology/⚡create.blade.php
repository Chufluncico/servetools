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
            'ip' => ['ip'],
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

<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ __('rx.new_modality') }}
        </flux:heading>

        <flux:input wire:model="name" label="{{ __('rx.modality_name') }}" />

<flux:field>
    <flux:label>Website</flux:label>

    <flux:input.group>

        <flux:input wire:model="website" placeholder="example.com" />
        <flux:input wire:model="website" placeholder="example.com" />
        <flux:input wire:model="website" placeholder="example.com" />
        <flux:input wire:model="website" placeholder="example.com" />

    </flux:input.group>

    <flux:error name="website" />
</flux:field>


        <div class="flex justify-end gap-3">
            <flux:button type="button" wire:click="$set('showModal', false)">
                {{ __('Cancel') }}
            </flux:button>

            <flux:button variant="primary" wire:click="create" wire:loading.attr="disabled">
                {{ __('Save') }}
            </flux:button>
        </div>
    </div>
</flux:modal>