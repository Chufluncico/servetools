<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="hospital" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
                Inicio
            </flux:sidebar.item>

            <flux:sidebar.group expandable :expanded="request()->routeIs('radiologia.*')" icon="radiation" :heading="__('Radiologia')" class="grid">
                <flux:sidebar.item :href="route('radiologia.modalidades')" :current="request()->routeIs('radiologia.modalidades')">
                    {{ __('Inventario Modalidades') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="settings" :href="route('adminPanel.index')" :current="request()->routeIs('adminPanel.index')">Admin Panel</flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>



    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <x-desktop-user-menu class="lg:hidden" :name="auth()->user()->name" />
        
    </flux:header>


        {{ $slot }}

        @fluxScripts
    </body>
</html>

