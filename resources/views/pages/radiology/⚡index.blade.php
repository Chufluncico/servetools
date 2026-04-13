<?php

use App\Models\Modalidad;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

new #[Title('Modalities')] class extends Component
{
    use WithPagination;

    public array $options = [

        'search' => '',

        'searchFields' => [
            'aet' => [
                'label' => 'AE Title',
                'enabled' => true,
            ],
            'ip' => [
                'label' => 'Dirección IP',
                'enabled' => true,
            ],
            'location' => [
                'label' => 'Ubicación',
                'enabled' => true,
            ],
            'department' => [
                'label' => 'Servicio',
                'enabled' => true,
            ],
            'model' => [
                'label' => 'Modelo',
                'enabled' => true,
            ],
        ],

        'filters' => [
            'department' => [],
            'modalidad'  => [],
        ],

        'sort' => [
            'by' => 'aet',
            'direction' => 'asc',
        ],
    ];

    public function mount()
    {
        $this->authorize('viewAny', Modalidad::class);
    }

    public function updatedOptions()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->options['filters'] = [
            'department' => [],
            'modalidad'  => [],
        ];

        $this->resetPage();
    }

    #[On('modality-created')]
    #[On('modality-updated')]
    #[On('modality-deleted')]
    #[On('modalities-imported')]
    public function refresh()
    {
        $this->resetPage();
    }

    #[Computed]
    public function modalidades()
    {
        $query = Modalidad::query();

        // 🔎 SEARCH
        $search = trim($this->options['search']);

        if ($search !== '') {

            $search = "%{$search}%";

            $query->where(function ($q) use ($search) {

                foreach ($this->options['searchFields'] as $field => $config) {
                    if ($config['enabled']) {
                        $q->orWhere($field, 'like', $search);
                    }
                }

            });
        }

        // 🎯 FILTERS

        $departments = collect($this->options['filters']['department'])
            ->filter()
            ->keys()
            ->values()
            ->toArray();

        if (!empty($departments)) {
            $query->whereIn('department', $departments);
        }

        $modalidades = collect($this->options['filters']['modalidad'])
            ->filter()
            ->keys()
            ->values()
            ->toArray();

        if (!empty($modalidades)) {
            $query->whereIn('modalidad', $modalidades);
        }

        // 🔁 SORT
        $query->orderBy(
            $this->options['sort']['by'],
            $this->options['sort']['direction']
        );

        return $query->paginate(10);
    }
};
?>


<section class="w-full">
    <x-pages::radiology.layout :heading="__('rx.inventory_heading')" :subheading="__('rx.inventory_subheading')">
        <div class="border-1 p-4 rounded-md border-zinc-200 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700">
            <div class="flex mb-4 space-x-2">
                {{-- BUSCADOR --}}
                <flux:input.group class="flex-3">
                    <flux:input
                        icon="magnifying-glass"
                        wire:model.live.debounce.300ms="options.search"
                        placeholder="{{ __('rx.search_modality') }}"
                        clearable
                    />

                    {{-- CAMPOS DE BÚSQUEDA --}}
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">
                            Campo
                        </flux:button>

                        <flux:menu keep-open>
                            @foreach($options['searchFields'] as $field => $config)
                                <flux:menu.checkbox
                                    wire:model.live="options.searchFields.{{ $field }}.enabled"
                                >
                                    {{ $config['label'] }}
                                </flux:menu.checkbox>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </flux:input.group>

                {{-- OPCIONES --}}
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        Opciones
                    </flux:button>

                    <flux:menu keep-open class="w-64">
                        {{-- ORDEN --}}
                        <flux:menu.submenu heading="Ordenar por" keep-open>
                            <flux:menu.checkbox wire:model.live="options.sort.by" value="aet">
                                AET
                            </flux:menu.checkbox>

                            <flux:menu.checkbox wire:model.live="options.sort.by" value="department">
                                Servicio
                            </flux:menu.checkbox>

                            <flux:menu.checkbox wire:model.live="options.sort.by" value="centre">
                                Centro
                            </flux:menu.checkbox>

                            <flux:menu.separator />

                            <flux:menu.checkbox wire:model.live="options.sort.direction" value="asc">
                                Ascendente
                            </flux:menu.checkbox>

                            <flux:menu.checkbox wire:model.live="options.sort.direction" value="desc">
                                Descendente
                            </flux:menu.checkbox>
                        </flux:menu.submenu>

                        {{-- FILTROS --}}
                        <flux:menu.submenu heading="Filtros" keep-open>
                            {{-- DEPARTMENT --}}
                            <flux:menu.submenu heading="Departamento" keep-open>
                                @foreach(
                                    \App\Models\Modalidad::select('department')
                                        ->distinct()
                                        ->pluck('department')
                                        ->filter()
                                        ->sort()
                                        ->values()
                                    as $dep
                                )
                                    <flux:menu.checkbox
                                        wire:key="filter-department-{{ md5($dep) }}"
                                        wire:model.live="options.filters.department.{{ $dep }}"
                                    >
                                        {{ $dep }}
                                    </flux:menu.checkbox>
                                @endforeach
                            </flux:menu.submenu>

                            {{-- MODALIDAD --}}
                            <flux:menu.submenu heading="Modalidad" keep-open>
                                @foreach(
                                    \App\Models\Modalidad::select('modalidad')
                                        ->distinct()
                                        ->pluck('modalidad')
                                        ->filter()
                                        ->sort()
                                        ->values()
                                    as $mod
                                )
                                    <flux:menu.checkbox
                                        wire:key="filter-modalidad-{{ md5($mod) }}"
                                        wire:model.live="options.filters.modalidad.{{ $mod }}"
                                    >
                                        {{ $mod }}
                                    </flux:menu.checkbox>
                                @endforeach
                            </flux:menu.submenu>
                        </flux:menu.submenu>
                    </flux:menu>
                </flux:dropdown>

                <flux:button variant="ghost" wire:click="resetFilters">
                    Limpiar filtros
                </flux:button>

                <flux:spacer />

                <flux:button.group>
                    <livewire:radiology.import />
                    <livewire:radiology.export />
                </flux:button.group>

                @can('create', App\Models\Modalidad::class)
                    <flux:button
                        variant="primary"
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
        <livewire:radiology.edit />
    </x-pages::radiology.layout>
</section>