<x-layouts.app>
    <x-header />

    <main class="px-4 py-8">
        <div class="mx-auto max-w-6xl">
            <h1 class="mb-8 text-2xl font-semibold text-gray-900 dark:text-white">
                Mi Álbum USA 94
            </h1>

            {{-- Main content grid: Album + Stickers sidebar --}}
            <div class="mb-8 grid gap-6 lg:grid-cols-[1fr,320px]">
                {{-- Album Viewer --}}
                <div class="rounded-2xl bg-white p-4 shadow-xl sm:p-8 dark:bg-gray-800">
                    <h2 class="mb-6 text-center text-lg font-medium text-gray-900 dark:text-white">
                        Álbum de Cromos
                    </h2>
                    <livewire:album />
                </div>

                {{-- Stickers Sidebar --}}
                <div class="flex flex-col gap-6 lg:self-start lg:sticky lg:top-4">
                    {{-- Unglued Stickers Pile --}}
                    <div class="rounded-2xl bg-white p-4 shadow-xl sm:p-6 dark:bg-gray-800">
                        <h2 class="mb-4 text-center text-lg font-medium text-gray-900 dark:text-white">
                            Cromos Sin Pegar
                        </h2>
                        <livewire:unglued-stickers />
                    </div>

                    {{-- Duplicate Stickers Pile --}}
                    <div class="rounded-2xl bg-white p-4 shadow-xl sm:p-6 dark:bg-gray-800">
                        <h2 class="mb-4 text-center text-lg font-medium text-amber-600 dark:text-amber-400">
                            Cromos Repetidos
                        </h2>
                        <livewire:duplicate-stickers />
                    </div>
                </div>
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
