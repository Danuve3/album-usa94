<div class="flex flex-col">
    {{-- Header with stats --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        {{-- Total Stickers --}}
        <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-4 text-white shadow-lg">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $totalStickers }}</p>
                    <p class="text-xs text-emerald-100">Total cromos</p>
                </div>
            </div>
        </div>

        {{-- Unique Stickers --}}
        <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white shadow-lg">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $uniqueStickers }}</p>
                    <p class="text-xs text-blue-100">Cromos únicos</p>
                </div>
            </div>
        </div>

        {{-- Glued Count --}}
        <div class="rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 p-4 text-white shadow-lg">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ collect($stickers)->where('is_glued', true)->count() }}</p>
                    <p class="text-xs text-purple-100">Pegados</p>
                </div>
            </div>
        </div>

        {{-- Duplicates Count --}}
        <div class="rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 p-4 text-white shadow-lg">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ collect($stickers)->filter(fn($s) => $s['total_count'] > 1)->count() }}</p>
                    <p class="text-xs text-amber-100">Con repetidos</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Filter Tabs --}}
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="setFilter('all')"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}"
            >
                Todos
            </button>
            <button
                wire:click="setFilter('glued')"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $filter === 'glued' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}"
            >
                Pegados
            </button>
            <button
                wire:click="setFilter('unglued')"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $filter === 'unglued' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}"
            >
                Sin pegar
            </button>
            <button
                wire:click="setFilter('duplicates')"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $filter === 'duplicates' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}"
            >
                Repetidos
            </button>
        </div>

        {{-- Search --}}
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar por # o nombre..."
                class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 sm:w-64"
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

    {{-- Results count --}}
    <div class="mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Mostrando <span class="font-semibold text-gray-900 dark:text-white">{{ count($filteredStickers) }}</span> cromos
        </p>
    </div>

    {{-- Stickers Grid --}}
    @if (count($filteredStickers) > 0)
        <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8">
            @foreach ($filteredStickers as $sticker)
                <div
                    class="sticker-card group relative aspect-[3/4] overflow-hidden rounded-xl shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-xl {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny ring-2 ring-amber-400' : 'bg-white dark:bg-gray-700' }}"
                >
                    {{-- Sticker Image --}}
                    <div class="flex h-full w-full items-center justify-center bg-gray-100 p-2 dark:bg-gray-800">
                        @if ($sticker['image_path'])
                            <img
                                src="{{ Storage::url($sticker['image_path']) }}"
                                alt="{{ $sticker['name'] }}"
                                class="h-full w-full rounded-lg object-contain"
                                loading="lazy"
                            />
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-600">
                                <span class="text-2xl font-bold text-gray-500 dark:text-gray-400">
                                    {{ $sticker['number'] }}
                                </span>
                                <span class="mt-1 text-[10px] text-gray-400 dark:text-gray-500">Sin imagen</span>
                            </div>
                        @endif
                    </div>

                    {{-- Number Badge (bottom left) --}}
                    <div class="absolute bottom-1.5 left-1.5 rounded-md bg-black/70 px-1.5 py-0.5 text-xs font-bold text-white backdrop-blur-sm">
                        #{{ $sticker['number'] }}
                    </div>

                    {{-- Quantity Badge (top right) --}}
                    @if ($sticker['total_count'] > 0)
                        <div class="absolute -right-1 -top-1 flex h-7 min-w-7 items-center justify-center rounded-full {{ $sticker['total_count'] > 1 ? 'bg-amber-500' : 'bg-emerald-500' }} px-1.5 text-xs font-bold text-white shadow-lg">
                            x{{ $sticker['total_count'] }}
                        </div>
                    @endif

                    {{-- Glued indicator (bottom right) --}}
                    @if ($sticker['is_glued'])
                        <div class="absolute bottom-1.5 right-1.5 rounded-md bg-emerald-500/90 px-1.5 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                            <svg class="inline h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    @endif

                    {{-- Shiny Badge --}}
                    @if ($sticker['rarity'] === 'shiny')
                        <div class="absolute left-1.5 top-1.5 rounded-md bg-amber-500/90 px-1.5 py-0.5 text-[10px] font-bold text-white backdrop-blur-sm">
                            ✦ Shiny
                        </div>
                    @endif

                    {{-- Hover Overlay with Details --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/80 p-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                        <p class="mb-2 text-center text-xs font-semibold text-white line-clamp-2">
                            {{ $sticker['name'] }}
                        </p>
                        <div class="flex flex-col gap-1 text-center text-[10px] text-gray-300">
                            <span>Página {{ $sticker['page_number'] }}</span>
                            <span class="flex items-center justify-center gap-1">
                                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                                {{ $sticker['glued_count'] }} pegado{{ $sticker['glued_count'] !== 1 ? 's' : '' }}
                            </span>
                            <span class="flex items-center justify-center gap-1">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-400"></span>
                                {{ $sticker['unglued_count'] }} sin pegar
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 py-16 dark:border-gray-700 dark:bg-gray-800/50">
            @if ($search || $filter !== 'all')
                {{-- No results for filters --}}
                <svg class="mb-4 h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No se encontraron cromos</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Prueba con otros filtros o términos de búsqueda</p>
                <button
                    wire:click="$set('search', ''); $set('filter', 'all')"
                    class="mt-4 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-600"
                >
                    Limpiar filtros
                </button>
            @else
                {{-- No stickers at all --}}
                <svg class="mb-4 h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No tienes cromos todavía</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Abre sobres para empezar tu colección</p>
            @endif
        </div>
    @endif

    <style>
        .sticker-shiny {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fbbf24 100%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        .sticker-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
