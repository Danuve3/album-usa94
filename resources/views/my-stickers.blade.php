<x-layouts.app>
    <x-header />

    <main class="flex-1 px-4 py-6">
        <div class="mx-auto max-w-7xl">
            <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-6 dark:bg-gray-800">
                <h1 class="mb-6 flex items-center gap-3 text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">
                    <svg class="h-7 w-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Mis Cromos
                </h1>
                <livewire:my-stickers />
            </div>
        </div>
    </main>

    <x-footer />
</x-layouts.app>
