<div class="p-2 flex border rounded-lg bg-white dark:bg-zinc-600">
    <div class="ps-2 py-2 pe-0 flex items-baseline">
        <flux:badge>{{ $modalidad->modalidad }}</flux:badge>
    </div>
    
    <div class="ps-2 flex-1 ">
        <div class="flex-1 py-2 pe-3 flex flex-col justify-center gap-2">
            <div class="flex items-center gap-2">
                <flux:heading size="lg">{{ $modalidad->aet }}</flux:heading>
                <flux:text>{{ $modalidad->model }}</flux:text>
            </div>
            
            <div class="flex space-x-2">
                <flux:text variant="strong" class="flex-1">
                    Centro:
                    <span class="text-zinc-500">{{ $modalidad->centre }}</span>
                </flux:text>
                <flux:text variant="strong" class="flex-1">
                    Servicio:
                    <span class="text-zinc-500">{{ $modalidad->department }}</span>
                </flux:text>
                <flux:text variant="strong" class="flex-1">
                    Ubicación:
                    <span class="text-zinc-500">{{ $modalidad->location }}</span>
                </flux:text>
            </div>
        </div>

        <div class="py-2 self-start flex items-center gap-2">
            <flux:text>{{ $modalidad->ip }}</flux:text>
            
            <flux:text>{{ $modalidad->machine }}</flux:text>
            <flux:text>{{ $modalidad->station }}</flux:text>
            <flux:text>{{ $modalidad->request_date }}</flux:text>
            <flux:text>{{ $modalidad->observations }}</flux:text>
            <flux:text>{{ $modalidad->syngo }}</flux:text>  
        </div>
    </div>

    <div class="ps-2">
        @can('update', $modalidad)
            @include('partials.radiology.modalidad-options')
        @endcan
    </div>
</div>