<div class="flex flex-col">
    {{-- Header with counter and search --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Counter --}}
        <div class="flex items-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCount }}</span>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $totalCount === 1 ? 'cromo sin pegar' : 'cromos sin pegar' }}
                </p>
            </div>
        </div>

        {{-- Search/Filter --}}
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

    {{-- Stickers Fan Display --}}
    @if (count($filteredStickers) > 0)
        <div
            class="stickers-pile max-h-96 overflow-y-auto rounded-lg border border-gray-200 bg-gradient-to-br from-emerald-50 to-gray-100 p-4 dark:border-gray-700 dark:from-gray-800 dark:to-gray-900"
            x-data="ungluedStickers()"
        >
            {{-- Fan container with flex-wrap for multiple rows --}}
            <div class="stickers-fan flex flex-wrap justify-center gap-y-8 py-4">
                @foreach ($filteredStickers as $index => $sticker)
                    @php
                        // Generate pseudo-random rotation based on sticker ID for consistency
                        $seed = $sticker['id'] * 7;
                        $rotation = (($seed % 17) - 8); // Range: -8 to 8 degrees
                    @endphp
                    <div
                        class="fan-card group relative aspect-[3/4] w-16 cursor-grab select-none rounded-lg shadow-lg transition-all duration-300 ease-out sm:w-20 {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny ring-2 ring-amber-400' : 'bg-white dark:bg-gray-700' }}"
                        style="
                            --rotation: {{ $rotation }}deg;
                            margin-left: {{ $index === 0 ? '0' : '-24px' }};
                            transform: rotate(var(--rotation));
                            z-index: {{ $index + 1 }};
                        "
                        draggable="true"
                        data-sticker-id="{{ $sticker['id'] }}"
                        data-user-sticker-id="{{ $sticker['user_sticker_id'] }}"
                        data-sticker-number="{{ $sticker['number'] }}"
                        data-sticker-name="{{ $sticker['name'] }}"
                        data-page-number="{{ $sticker['page_number'] }}"
                        @dragstart="onDragStart($event, {{ json_encode($sticker) }})"
                        @dragend="onDragEnd($event)"
                        @mouseenter="$el.style.zIndex = 999"
                        @mouseleave="$el.style.zIndex = {{ $index + 1 }}"
                    >
                        {{-- Sticker Content --}}
                        <div class="flex h-full flex-col items-center justify-center overflow-hidden rounded-lg p-1">
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
                        <div class="absolute bottom-1 left-1 rounded bg-black/70 px-1.5 py-0.5 text-[10px] font-bold text-white backdrop-blur-sm">
                            #{{ $sticker['number'] }}
                        </div>

                        {{-- Count Badge (if more than 1) --}}
                        @if ($sticker['count'] > 1)
                            <div class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-[10px] font-bold text-white shadow-md ring-2 ring-white dark:ring-gray-800">
                                {{ $sticker['count'] }}
                            </div>
                        @endif

                        {{-- Shiny Badge --}}
                        @if ($sticker['rarity'] === 'shiny')
                            <div class="absolute right-1 top-1 text-[8px] font-bold text-amber-700">
                                ✦
                            </div>
                        @endif

                        {{-- Tooltip --}}
                        <div class="pointer-events-none absolute bottom-full left-1/2 z-[1000] mb-2 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                            {{ $sticker['name'] }}
                            <span class="text-gray-400">(Pág. {{ $sticker['page_number'] }})</span>
                            <div class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Drag instruction --}}
        <p class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">
            Arrastra los cromos al album para pegarlos
        </p>
    @else
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 py-12 dark:border-gray-700 dark:bg-gray-800/50">
            @if ($search)
                {{-- No results for search --}}
                <svg class="mb-3 h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No se encontraron cromos</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Prueba con otro término de búsqueda</p>
            @else
                {{-- No stickers at all --}}
                <svg class="mb-3 h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No tienes cromos sin pegar</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Abre sobres para conseguir más cromos</p>
            @endif
        </div>
    @endif

    <script>
        function ungluedStickers() {
            return {
                draggingSticker: null,

                onDragStart(event, sticker) {
                    this.draggingSticker = sticker;

                    // Set drag data
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('application/json', JSON.stringify(sticker));
                    event.dataTransfer.setData('text/plain', sticker.number.toString());

                    // Add dragging class
                    event.target.classList.add('opacity-50', 'scale-95');

                    // Dispatch custom event for album to listen
                    window.dispatchEvent(new CustomEvent('sticker-drag-start', {
                        detail: sticker
                    }));
                },

                onDragEnd(event) {
                    // Remove dragging class
                    event.target.classList.remove('opacity-50', 'scale-95');

                    // Dispatch custom event
                    window.dispatchEvent(new CustomEvent('sticker-drag-end', {
                        detail: this.draggingSticker
                    }));

                    this.draggingSticker = null;
                }
            }
        }
    </script>

    <style>
        .stickers-pile::-webkit-scrollbar {
            width: 6px;
        }

        .stickers-pile::-webkit-scrollbar-track {
            background: transparent;
        }

        .stickers-pile::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .stickers-pile::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.7);
        }

        .dark .stickers-pile::-webkit-scrollbar-thumb {
            background-color: rgba(75, 85, 99, 0.5);
        }

        .dark .stickers-pile::-webkit-scrollbar-thumb:hover {
            background-color: rgba(75, 85, 99, 0.7);
        }

        /* Fan card styles */
        .fan-card {
            touch-action: none;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06),
                -2px 0 8px -2px rgba(0, 0, 0, 0.1);
        }

        .fan-card:hover {
            transform: rotate(0deg) translateY(-20px) scale(1.15) !important;
            box-shadow:
                0 20px 25px -5px rgba(0, 0, 0, 0.2),
                0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }

        .fan-card.dragging {
            opacity: 0.5;
            transform: rotate(0deg) scale(0.95) !important;
        }

        /* Responsive adjustments for fan overlap */
        @media (max-width: 640px) {
            .stickers-fan .fan-card {
                margin-left: -20px !important;
            }
            .stickers-fan .fan-card:first-child {
                margin-left: 0 !important;
            }
        }

        /* Shiny sticker animation */
        .sticker-shiny {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fbbf24 100%);
            background-size: 200% 200%;
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
    </style>
</div>
