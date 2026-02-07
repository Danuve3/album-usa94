<x-layouts.app>
    <x-header />

    <main class="px-4 py-8">
        <div class="mx-auto max-w-6xl">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Obtener Sobres
                </h1>
                <a
                    href="{{ route('album') }}"
                    class="flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Álbum
                </a>
            </div>

            <livewire:redeem-code />
        </div>
    </main>
</x-layouts.app>
