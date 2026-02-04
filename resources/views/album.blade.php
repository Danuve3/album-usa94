<x-layouts.app>
    <x-header />

    <main class="flex-1 px-4 py-6">
        <div class="mx-auto max-w-[1920px]">
            {{-- Two-column layout: Album | Sidebar --}}
            <div class="flex flex-col gap-6 lg:flex-row">
                {{-- Left: Album Viewer --}}
                <div class="w-full min-w-0 lg:flex-1">
                    <div class="overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800">
                        <livewire:album />
                    </div>

                    {{-- Duplicate Stickers below album --}}
                    <div class="mt-4 rounded-2xl bg-white p-4 shadow-lg sm:p-6 dark:bg-gray-800">
                        <h2 class="mb-4 flex items-center justify-center gap-2 text-sm font-semibold text-amber-600 dark:text-amber-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Cromos Repetidos
                        </h2>
                        <livewire:duplicate-stickers />
                    </div>
                </div>

                {{-- Right: Sidebar (25%) --}}
                <aside class="w-full lg:w-[25%] lg:sticky lg:top-4 lg:h-[calc(100vh-6rem)] lg:self-start">
                    <div class="flex h-full flex-col gap-4">
                        {{-- Unglued Stickers --}}
                        <div class="flex-1 overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800">
                            <div class="flex h-full flex-col">
                                <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                                    <h2 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        Cromos Sin Pegar
                                    </h2>
                                </div>
                                <div class="flex-1 overflow-y-auto overflow-x-hidden p-4">
                                    <livewire:unglued-stickers />
                                </div>
                            </div>
                        </div>

                        {{-- Pack Pile --}}
                        <div id="pack-pile" class="flex-1 overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800">
                            <div class="flex h-full flex-col">
                                <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                                    <h2 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Mis Sobres
                                    </h2>
                                </div>
                                <div class="flex-1 overflow-y-auto overflow-x-hidden p-4">
                                    <livewire:pack-pile />
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <x-footer />
</x-layouts.app>
