<div class="flex items-start flex-col">
    <div class="relative w-full mb-6">
        <flux:heading size="xl" level="1">{{ $heading ?? '' }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ $subheading ?? '' }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex-1 self-stretch max-md:pt-6">
        {{ $slot }}
    </div>
</div>
