<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

    	
    	<flux:breadcrumbs>
		    <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
		    <flux:breadcrumbs.item href="#">Blog</flux:breadcrumbs.item>
		    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
		</flux:breadcrumbs>

        
		<livewire:admin-panel.index />


    </div>
</x-layouts::app>



