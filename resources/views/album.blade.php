<x-layouts.app>
    <x-header />

    <main class="px-4 py-8">
        <div class="mx-auto max-w-6xl">
            <h1 class="mb-8 text-2xl font-semibold text-gray-900 dark:text-white">
                Mi Álbum USA 94
            </h1>

            {{-- Album Viewer --}}
            <div class="mb-8 rounded-2xl bg-white p-4 sm:p-8 shadow-xl dark:bg-gray-800">
                <h2 class="mb-6 text-center text-lg font-medium text-gray-900 dark:text-white">
                    Álbum de Cromos
                </h2>
                <livewire:album />
            </div>

            {{-- Pack Pile --}}
            <div class="mb-8 rounded-2xl bg-white p-8 shadow-xl dark:bg-gray-800">
                <h2 class="mb-6 text-center text-lg font-medium text-gray-900 dark:text-white">
                    Mis Sobres
                </h2>
                <livewire:pack-pile />
            </div>
        </div>
    </main>
</x-layouts.app>
