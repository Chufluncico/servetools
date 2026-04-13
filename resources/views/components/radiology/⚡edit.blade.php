<?php

use App\Models\Modalidad;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;


new class extends Component {

    public ?Modalidad $modalidad = null;
    public string $name = '';
    public ?string $ip = '';
    public bool $showModal = false;


    #[On('edit-modalidad')]
    public function openEditModal(int $modalidadId)
    {
        $this->modalidad = Modalidad::findOrFail($modalidadId);
        $this->authorize('update', $this->modalidad);
        $this->name = $this->modalidad->name;
        $this->ip = $this->modalidad->ip;
        $this->showModal = true;
    }

    public function edit(): void
    {
        $modalidad = $this->modalidad;
        $this->authorize('update', $modalidad);
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('modalidades', 'name')->ignore($modalidad->id),],
            'ip' => ['ip',],
        ]);
        $modalidad->update([
            'name' => $validated['name'],
            'ip' => $validated['ip'],
        ]);
        $this->dispatch('modalidad-updated', name: $modalidad->name);
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['modalidad', 'name', 'ip']);
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

};
?>


<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ __('rx.edit_modality') }}
        </flux:heading>

        <flux:input wire:model="name" label="{{ __('User name') }}" />
        
        <flux:input 
            wire:model="ip"
            label="{{ __('rx.ip_address') }}"
            pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}" 
            x-mask:dynamic="[ ...$input.split('.').map( (x, index, array) => '9'.repeat( x.length > 0 && x.length < 3 ? x.length + ( index === array.length -1 ? 1 : 0 ) : 3 ) ),'999','999','999','999'].splice(0,4).join('.')"
        />

        <div class="flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" wire:click="edit" wire:loading.attr="disabled">
                {{ __('Save') }}
            </flux:button>
        </div>
    </div>
</flux:modal>