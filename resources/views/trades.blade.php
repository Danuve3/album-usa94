<x-layouts.app>
    <x-header />

    <main class="px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Intercambios
                </h1>
                <a
                    href="{{ route('album') }}"
                    class="flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Album
                </a>
            </div>

            {{-- Trade Inbox --}}
            <div class="mb-8">
                <livewire:trade-inbox />
            </div>

            {{-- Separator --}}
            <div class="relative mb-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-100 px-4 text-sm font-medium text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                        Nueva Propuesta
                    </span>
                </div>
            </div>

            {{-- Trade Proposal --}}
            <livewire:trade-proposal />
        </div>
    </main>
</x-layouts.app>
