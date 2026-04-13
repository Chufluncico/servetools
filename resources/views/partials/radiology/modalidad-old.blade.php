<div class="bg-white dark:bg-zinc-600 shadow-md rounded-lg">
    <div class="px-6 py-5">
        <div class="flex items-start">
            <flux:text>{{ $modalidad->modalidad }}</flux:text>
            <!-- Card content -->
            <div class="flex-grow truncate">
                <!-- Card header -->
                <div class="w-full sm:flex justify-between items-center mb-3">
                    <!-- Title -->
                    <h3 class="text-2xl leading-snug font-extrabold truncate mb-1 sm:mb-0">{{ $modalidad->aet }}</h3>
                    <!-- Like and comment buttons -->
                    <div class="flex-shrink-0 flex items-center space-x-3 sm:ml-2">
                        @can('update', $modalidad)
                            @include('partials.radiology.modalidad-options')
                        @endcan
                    </div>
                </div>
                <!-- Card body -->
                <div class="flex items-end justify-between whitespace-normal">
                    <flux:text>{{ $modalidad->department }}</flux:text>
                    <flux:text>{{ $modalidad->centre }}</flux:text>
                    <flux:text>{{ $modalidad->location }}</flux:text>
                    <flux:text>{{ $modalidad->ip }}</flux:text>
                    <flux:text>{{ $modalidad->model }}</flux:text>
                    
                    <flux:text>{{ $modalidad->machine }}</flux:text>
                    <flux:text>{{ $modalidad->station }}</flux:text>
                    <flux:text>{{ $modalidad->request_date }}</flux:text>
                    <flux:text>{{ $modalidad->observations }}</flux:text>
                    <flux:text>{{ $modalidad->syngo }}</flux:text>
                </div>
            </div>
        </div>
    </div>
</div>
