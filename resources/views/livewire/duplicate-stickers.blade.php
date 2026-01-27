<div class="flex flex-col">
    {{-- Header with counter and search --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Counter --}}
        <div class="flex items-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalDuplicates }}</span>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $totalDuplicates === 1 ? 'cromo repetido' : 'cromos repetidos' }}
                </p>
            </div>
        </div>

        {{-- Search/Filter --}}
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar por # o nombre..."
                class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-500 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 sm:w-64"
            />
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            @if ($search)
                <button
                    wire:click="$set('search', '')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Duplicates Grid --}}
    @if (count($filteredStickers) > 0)
        <div class="duplicates-pile max-h-80 overflow-y-auto rounded-lg border-2 border-dashed border-amber-300 bg-amber-50/50 p-3 dark:border-amber-700 dark:bg-amber-900/10">
            <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-4">
                @foreach ($filteredStickers as $sticker)
                    <div
                        class="duplicate-sticker group relative aspect-[3/4] cursor-pointer select-none rounded-lg shadow-md transition-all duration-200 hover:scale-105 hover:shadow-lg {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}"
                        data-sticker-id="{{ $sticker['id'] }}"
                        data-sticker-number="{{ $sticker['number'] }}"
                        data-sticker-name="{{ $sticker['name'] }}"
                        x-data="{ showActions: false }"
                        @click="showActions = !showActions"
                    >
                        {{-- Sticker Content --}}
                        <div class="flex h-full flex-col items-center justify-center p-1">
                            @if ($sticker['image_path'])
                                <img
                                    src="{{ Storage::url($sticker['image_path']) }}"
                                    alt="{{ $sticker['name'] }}"
                                    class="h-full w-full rounded object-contain"
                                    loading="lazy"
                                />
                            @else
                                <span class="text-lg font-bold {{ $sticker['rarity'] === 'shiny' ? 'text-amber-800' : 'text-gray-800 dark:text-white' }}">
                                    {{ $sticker['number'] }}
                                </span>
                            @endif
                        </div>

                        {{-- Number Badge --}}
                        <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">
                            #{{ $sticker['number'] }}
                        </div>

                        {{-- Duplicate Count Badge (x3, x4, etc) --}}
                        <div class="absolute -right-1 -top-1 flex h-6 min-w-6 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[11px] font-bold text-white shadow-md">
                            x{{ $sticker['extra_count'] }}
                        </div>

                        {{-- Glued indicator --}}
                        @if ($sticker['is_glued'])
                            <div class="absolute bottom-1 right-1 rounded bg-emerald-500/80 px-1 py-0.5 text-[8px] font-medium text-white">
                                Pegado
                            </div>
                        @endif

                        {{-- Shiny Badge --}}
                        @if ($sticker['rarity'] === 'shiny')
                            <div class="absolute left-1 top-1 text-[10px] font-bold text-amber-700">
                                ✦
                            </div>
                        @endif

                        {{-- Tooltip --}}
                        <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                            {{ $sticker['name'] }}
                            <span class="text-gray-400">(x{{ $sticker['extra_count'] }} repetidos)</span>
                            <div class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                        </div>

                        {{-- Quick Actions Overlay --}}
                        <div
                            x-show="showActions"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            @click.away="showActions = false"
                            class="absolute inset-0 flex flex-col items-center justify-center gap-1 rounded-lg bg-black/80 p-1"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-center gap-1 rounded bg-amber-500 px-2 py-1.5 text-[10px] font-semibold text-white transition hover:bg-amber-600"
                                @click.stop="$dispatch('open-trade-modal', { stickerId: {{ $sticker['id'] }}, stickerNumber: {{ $sticker['number'] }}, stickerName: '{{ addslashes($sticker['name']) }}', extraCount: {{ $sticker['extra_count'] }} })"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                Intercambiar
                            </button>
                            <button
                                type="button"
                                class="flex w-full items-center justify-center gap-1 rounded bg-gray-600 px-2 py-1.5 text-[10px] font-semibold text-white transition hover:bg-gray-700"
                                @click.stop="$dispatch('view-sticker-detail', { stickerId: {{ $sticker['id'] }} }); showActions = false"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver detalle
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Trade instruction --}}
        <p class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">
            Toca un cromo para ver opciones de intercambio
        </p>
    @else
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-amber-300 bg-amber-50/30 py-12 dark:border-amber-800 dark:bg-amber-900/10">
            @if ($search)
                {{-- No results for search --}}
                <svg class="mb-3 h-12 w-12 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No se encontraron cromos repetidos</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Prueba con otro término de búsqueda</p>
            @else
                {{-- No duplicates at all --}}
                <svg class="mb-3 h-12 w-12 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No tienes cromos repetidos</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Abre más sobres para conseguir duplicados</p>
            @endif
        </div>
    @endif

    <style>
        .duplicates-pile::-webkit-scrollbar {
            width: 6px;
        }

        .duplicates-pile::-webkit-scrollbar-track {
            background: transparent;
        }

        .duplicates-pile::-webkit-scrollbar-thumb {
            background-color: rgba(245, 158, 11, 0.4);
            border-radius: 3px;
        }

        .duplicates-pile::-webkit-scrollbar-thumb:hover {
            background-color: rgba(245, 158, 11, 0.6);
        }

        .dark .duplicates-pile::-webkit-scrollbar-thumb {
            background-color: rgba(180, 83, 9, 0.4);
        }

        .dark .duplicates-pile::-webkit-scrollbar-thumb:hover {
            background-color: rgba(180, 83, 9, 0.6);
        }
    </style>
</div>
