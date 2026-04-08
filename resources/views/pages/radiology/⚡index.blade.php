<?php

use App\Models\User;
use App\Models\Modalidad;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

new #[Title('Modalities')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';


    public function mount()
    {
        $this->authorize('viewAny', Modalidad::class);
    }

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('modality-created')]
    #[On('modality-updated')]
    #[On('modality-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    #[Computed]
    public function modalidades()
    {
        return Modalidad::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', $search);
                });
            })
            ->orderByRaw('LOWER(' . $this->sortBy . ') ' . $this->sortDirection)
            ->paginate(10);
    }

};
?>

<section class="w-full">
    <x-pages::radiology.layout :heading="__('rx.inventory_heading')" :subheading="__('rx.inventory_subheading')">
        <div class="border-1 p-4 rounded-md border-zinc-200 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700">
            <div class="flex mb-4">
                <flux:input class="flex-1"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search modality') }}..."
                    clearable
                />

                <flux:spacer />

                @can('create', App\Models\Modalidad::class)
                    <flux:button variant="primary"
                        wire:click="$dispatch('create-modality')"
                    >
                        {{ __('rx.add_modality') }}
                    </flux:button>
                @endcan
            </div>        

            <div class="flex-col space-y-3 mt-6">
                @forelse($this->modalidades as $modalidad)

                    @include('partials.radiology.modalidad')
       
                    <div class="bg-white rounded-md p-6 border-1 border-zinc-200 dark:border-zinc-600">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg">
                                {{ $modalidad->name }}
                            </h3>
                            @can('modalidades.edit')
                                <flux:button
                                    size="xs"
                                    wire:click="$dispatch('editar-modalidad', { id: {{ $modalidad->id }} })"
                                >
                                    Editar
                                </flux:button>
                            @endcan
                        </div>
                        <p class="text-2xl font-bold text-gray-800">$24,580</p>
                        <p class="text-sm text-green-600 mt-2">↑ 12.5% from last month</p>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-10">
                        No se encontraron modalidades
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $this->modalidades->links() }}
            </div>
        </div>

        <livewire:radiology.create />
    </x-pages::radiology.layout>
</section>