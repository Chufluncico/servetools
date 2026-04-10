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

    public function updatedSearchField()
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

    public function export()
{
    $fileName = 'modalidades_' . now()->format('Y-m-d_H-i-s') . '.csv';

    $modalidades = Modalidad::query()
        ->when(trim($this->search) !== '', function ($query) {

            $search = '%' . trim($this->search) . '%';

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('ip', 'like', $search)
                  ->orWhere('location', 'like', $search)
                  ->orWhere('department', 'like', $search)
                  ->orWhere('aet', 'like', $search);
            });
        })
        ->orderByRaw('LOWER(' . $this->sortBy . ') ' . $this->sortDirection)
        ->get();

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename={$fileName}",
    ];

    $callback = function () use ($modalidades) {

        $file = fopen('php://output', 'w');

        // BOM UTF-8 para Excel Windows
        fwrite($file, "\xEF\xBB\xBF");

        fputcsv($file, [
            'Nombre',
            'IP',
            'Ubicación',
            'Departamento',
            'AET',
            'Fecha creación',
        ], ';');

        foreach ($modalidades as $modalidad) {
            fputcsv($file, [
                $modalidad->name,
                $modalidad->ip,
                $modalidad->location,
                $modalidad->department,
                $modalidad->aet,
                optional($modalidad->created_at)->format('d/m/Y H:i'),
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    #[Computed]
    public function modalidades()
    {
        return Modalidad::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', $search)
                      ->orWhere('ip', 'like', $search)
                      ->orWhere('location', 'like', $search)
                      ->orWhere('department', 'like', $search)
                      ->orWhere('aet', 'like', $search);
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
            <div class="flex mb-4 space-x-2">
                <flux:input icon="magnifying-glass" class="flex-1"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('rx.search_modality') }}..."
                    clearable
                />

                <flux:spacer />

                <flux:button wire:click="export">Exportar</flux:button>

                @can('create', App\Models\Modalidad::class)
                    <flux:button variant="primary"
                        wire:click="$dispatch('create-modality')"
                    >
                        {{ __('rx.add_modality') }}
                    </flux:button>
                @endcan
            </div>        

            <div class="flex-col space-y-4 mt-6">
                @forelse($this->modalidades as $modalidad)
                    @include('partials.radiology.modalidad')
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