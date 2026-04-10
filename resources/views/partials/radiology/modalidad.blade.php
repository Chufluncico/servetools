<div class="bg-white dark:bg-zinc-600 shadow-md rounded-lg">
    <div class="px-6 py-5">
        <div class="flex items-start">
            <!-- Icon -->
            <flux:icon.bolt />
            <!-- Card content -->
            <div class="flex-grow truncate">
                <!-- Card header -->
                <div class="w-full sm:flex justify-between items-center mb-3">
                    <!-- Title -->
                    <h3 class="text-2xl leading-snug font-extrabold truncate mb-1 sm:mb-0">{{ $modalidad->name }}</h3>
                    <!-- Like and comment buttons -->
                    <div class="flex-shrink-0 flex items-center space-x-3 sm:ml-2">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" icon:class="" />

                            <flux:menu>
                                @can('update', $modalidad)
                                    <flux:menu.item>
                                        {{ __('Edit') }}
                                    </flux:menu.item>
                                @endcan
                                @can('delete', $modalidad)
                                    <flux:menu.item 
                                        variant="danger"
                                    >
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
                <!-- Card body -->
                <div class="flex items-end justify-between whitespace-normal">
                    <!-- Paragraph -->
                    <div class="max-w-mdd">
                        <p class="mb-2">Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore.</p>
                    </div>
                    <!-- More link -->
                    <flux:icon.bolt />
                </div>
            </div>
        </div>
    </div>
</div>
