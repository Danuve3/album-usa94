<x-layouts.app>
    <x-header />

    <main class="flex-1 px-4 py-6">
        <div class="mx-auto max-w-4xl">
            {{-- Page Title --}}
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl dark:text-white">
                    Mis Estadisticas
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Seguimiento de tu progreso en el album USA 94
                </p>
            </div>

            {{-- Stats Component --}}
            <livewire:user-stats />
        </div>
    </main>

    <x-footer />
</x-layouts.app>
