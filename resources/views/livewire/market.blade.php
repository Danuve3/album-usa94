<div class="flex flex-col gap-6">
    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="rounded-lg bg-emerald-100 p-4 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search and Filter Section --}}
    <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
            Buscar en el Mercado
        </h2>

        <div class="flex flex-col gap-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="relative flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Buscar por numero o nombre de cromo..."
                    class="w-full rounded-lg border border-gray-300 bg-white py-3 pl-10 pr-4 text-gray-900 placeholder-gray-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                />
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- Filter Type --}}
            @if ($selectedStickerId)
                <select
                    wire:model.live="filterType"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
                    <option value="all">Todos</option>
                    <option value="offering">Ofrecen este cromo</option>
                    <option value="wanting">Buscan este cromo</option>
                </select>
            @endif
        </div>

        {{-- Search Results Dropdown --}}
        @if ($searchResults->count() > 0)
            <div class="mt-3 max-h-64 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                @foreach ($searchResults as $sticker)
                    <button
                        type="button"
                        wire:click="selectSticker({{ $sticker->id }})"
                        class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                    >
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $sticker->rarity->value === 'shiny' ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600' }} dark:bg-gray-800 dark:text-gray-300">
                            <span class="text-sm font-bold">#{{ $sticker->number }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $sticker->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cromo #{{ $sticker->number }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Active Filter Display --}}
        @if ($selectedStickerId)
            @php
                $selectedSticker = \App\Models\Sticker::find($selectedStickerId);
            @endphp
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Filtrando por:</span>
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    #{{ $selectedSticker->number }} - {{ $selectedSticker->name }}
                    <button type="button" wire:click="clearFilter" class="hover:text-emerald-900 dark:hover:text-emerald-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            </div>
        @endif
    </div>

    {{-- Quick Actions: Cromos que necesito --}}
    @if (count($listingsIWant) > 0)
        <div class="rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 p-6 shadow-xl">
            <h2 class="mb-4 flex items-center gap-2 text-lg font-medium text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                Cromos que Necesitas Disponibles
            </h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($listingsIWant->take(6) as $listing)
                    <button
                        type="button"
                        wire:click="openTradeModal({{ $listing->id }})"
                        class="group relative aspect-[3/4] overflow-hidden rounded-lg bg-white/10 shadow-md backdrop-blur-sm transition-all duration-200 hover:scale-105 hover:bg-white/20"
                    >
                        <div class="flex h-full flex-col items-center justify-center p-2">
                            @if ($listing->userSticker->sticker->image_path)
                                <img
                                    src="{{ Storage::url($listing->userSticker->sticker->image_path) }}"
                                    alt="{{ $listing->userSticker->sticker->name }}"
                                    class="h-full w-full rounded object-contain"
                                    loading="lazy"
                                />
                            @else
                                <span class="text-2xl font-bold text-white">
                                    {{ $listing->userSticker->sticker->number }}
                                </span>
                            @endif
                        </div>
                        <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">
                            #{{ $listing->userSticker->sticker->number }}
                        </div>
                        <div class="absolute right-1 top-1 rounded bg-emerald-500/80 px-1.5 py-0.5 text-[10px] font-bold text-white">
                            {{ $listing->user->name }}
                        </div>
                    </button>
                @endforeach
            </div>
            @if (count($listingsIWant) > 6)
                <p class="mt-3 text-center text-sm text-white/80">
                    Y {{ count($listingsIWant) - 6 }} cromos más que necesitas...
                </p>
            @endif
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Mis Cromos para Publicar --}}
        <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Publicar Cromo
                </h2>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    {{ count($myDuplicates) }} disponibles
                </span>
            </div>

            @if (count($myDuplicates) > 0)
                <div class="market-grid max-h-80 overflow-y-auto rounded-lg border-2 border-dashed border-emerald-300 bg-emerald-50/50 p-3 dark:border-emerald-700 dark:bg-emerald-900/10">
                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                        @foreach ($myDuplicates as $sticker)
                            <button
                                type="button"
                                wire:click="openPublishModal({{ $sticker['sticker_id'] }})"
                                class="group relative aspect-[3/4] rounded-lg shadow-md transition-all duration-200 hover:scale-105 hover:shadow-lg {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}"
                            >
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
                                <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                    #{{ $sticker['number'] }}
                                </div>
                                @if ($sticker['count'] > 1)
                                    <div class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-500 px-1 text-[10px] font-bold text-white shadow-md">
                                        x{{ $sticker['count'] }}
                                    </div>
                                @endif
                                <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                    {{ $sticker['name'] }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 py-12 dark:border-gray-600">
                    <svg class="mb-3 h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No tienes cromos disponibles</p>
                </div>
            @endif
        </div>

        {{-- Mis Publicaciones Activas --}}
        <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Mis Publicaciones
                </h2>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    {{ count($myListings) }} activas
                </span>
            </div>

            @if (count($myListings) > 0)
                <div class="space-y-3">
                    @foreach ($myListings as $listing)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                            <div class="flex items-center gap-4">
                                <div class="flex h-14 w-12 items-center justify-center rounded-lg {{ $listing->userSticker->sticker->rarity->value === 'shiny' ? 'bg-amber-100' : 'bg-gray-200' }} dark:bg-gray-700">
                                    @if ($listing->userSticker->sticker->image_path)
                                        <img
                                            src="{{ Storage::url($listing->userSticker->sticker->image_path) }}"
                                            alt="{{ $listing->userSticker->sticker->name }}"
                                            class="h-full w-full rounded object-contain"
                                        />
                                    @else
                                        <span class="text-lg font-bold text-gray-700 dark:text-white">
                                            #{{ $listing->userSticker->sticker->number }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        #{{ $listing->userSticker->sticker->number }} - {{ $listing->userSticker->sticker->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @if ($listing->wantedSticker)
                                            Busco: #{{ $listing->wantedSticker->number }} - {{ $listing->wantedSticker->name }}
                                        @else
                                            Acepto cualquier cromo
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="cancelListing({{ $listing->id }})"
                                class="rounded-lg bg-red-100 px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50"
                            >
                                Cancelar
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 py-12 dark:border-gray-600">
                    <svg class="mb-3 h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No tienes publicaciones activas</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Todas las Ofertas del Mercado --}}
    <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                Ofertas del Mercado
            </h2>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                {{ count($listings) }} ofertas
            </span>
        </div>

        @if (count($listings) > 0)
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($listings as $listing)
                    <div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-gray-50 transition hover:border-emerald-300 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900 dark:hover:border-emerald-700">
                        {{-- Sticker Preview --}}
                        <div class="relative aspect-[4/3] {{ $listing->userSticker->sticker->rarity->value === 'shiny' ? 'bg-gradient-to-br from-amber-100 to-amber-200' : 'bg-gradient-to-br from-gray-100 to-gray-200' }} dark:from-gray-800 dark:to-gray-700">
                            @if ($listing->userSticker->sticker->image_path)
                                <img
                                    src="{{ Storage::url($listing->userSticker->sticker->image_path) }}"
                                    alt="{{ $listing->userSticker->sticker->name }}"
                                    class="h-full w-full object-contain p-4"
                                    loading="lazy"
                                />
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <span class="text-4xl font-bold {{ $listing->userSticker->sticker->rarity->value === 'shiny' ? 'text-amber-600' : 'text-gray-600' }} dark:text-gray-300">
                                        #{{ $listing->userSticker->sticker->number }}
                                    </span>
                                </div>
                            @endif
                            @if ($listing->userSticker->sticker->rarity->value === 'shiny')
                                <div class="absolute right-2 top-2 rounded-full bg-amber-500 px-2 py-0.5 text-xs font-bold text-white">
                                    SHINY
                                </div>
                            @endif
                        </div>

                        {{-- Listing Info --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                #{{ $listing->userSticker->sticker->number }} - {{ $listing->userSticker->sticker->name }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Ofrecido por: {{ $listing->user->name }}
                            </p>

                            {{-- What they want --}}
                            <div class="mt-3 rounded-lg bg-amber-50 p-2 dark:bg-amber-900/20">
                                <p class="text-xs font-medium text-amber-700 dark:text-amber-400">
                                    @if ($listing->wantedSticker)
                                        Busca: #{{ $listing->wantedSticker->number }} - {{ $listing->wantedSticker->name }}
                                    @else
                                        Acepta cualquier cromo
                                    @endif
                                </p>
                            </div>

                            {{-- Trade Button --}}
                            <button
                                type="button"
                                wire:click="openTradeModal({{ $listing->id }})"
                                class="mt-4 w-full rounded-lg bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-emerald-700"
                            >
                                Proponer Intercambio
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 py-16 dark:border-gray-600">
                <svg class="mb-4 h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">No hay ofertas disponibles</h3>
                <p class="text-gray-500 dark:text-gray-400">Sé el primero en publicar un cromo en el mercado</p>
            </div>
        @endif
    </div>

    {{-- Publish Modal --}}
    @if ($showPublishModal && $publishStickerId)
        @php
            $publishingSticker = collect($myDuplicates)->firstWhere('sticker_id', $publishStickerId);
        @endphp
        @if ($publishingSticker)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                    Publicar Cromo en el Mercado
                </h3>

                {{-- Sticker being published --}}
                <div class="mb-6 flex items-center gap-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <div class="flex h-16 w-14 items-center justify-center rounded-lg {{ $publishingSticker['rarity'] === 'shiny' ? 'bg-amber-100' : 'bg-gray-200' }}">
                        @if ($publishingSticker['image_path'])
                            <img
                                src="{{ Storage::url($publishingSticker['image_path']) }}"
                                alt="{{ $publishingSticker['name'] }}"
                                class="h-full w-full rounded object-contain"
                            />
                        @else
                            <span class="text-xl font-bold text-gray-700">#{{ $publishingSticker['number'] }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            #{{ $publishingSticker['number'] }} - {{ $publishingSticker['name'] }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Tienes {{ $publishingSticker['count'] }} de este cromo
                        </p>
                    </div>
                </div>

                {{-- What do you want in exchange --}}
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        A cambio busco (opcional):
                    </label>
                    <select
                        wire:model="wantedStickerId"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Cualquier cromo</option>
                        @foreach ($neededStickers as $sticker)
                            <option value="{{ $sticker->id }}">
                                #{{ $sticker->number }} - {{ $sticker->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Si seleccionas un cromo específico, solo aparecerán ofertas de usuarios que lo tengan disponible.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <button
                        type="button"
                        wire:click="closePublishModal"
                        class="flex-1 rounded-lg bg-gray-200 px-4 py-3 font-semibold text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="publishListing"
                        class="flex-1 rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700"
                    >
                        Publicar
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif

    {{-- Trade Modal --}}
    @if ($showTradeModal && $selectedListing)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                    Proponer Intercambio
                </h3>

                {{-- What you'll get --}}
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <h4 class="mb-2 flex items-center gap-2 font-medium text-emerald-700 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                        Recibirás
                    </h4>
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-14 items-center justify-center rounded-lg {{ $selectedListing->userSticker->sticker->rarity->value === 'shiny' ? 'bg-amber-100' : 'bg-gray-200' }}">
                            @if ($selectedListing->userSticker->sticker->image_path)
                                <img
                                    src="{{ Storage::url($selectedListing->userSticker->sticker->image_path) }}"
                                    alt="{{ $selectedListing->userSticker->sticker->name }}"
                                    class="h-full w-full rounded object-contain"
                                />
                            @else
                                <span class="text-xl font-bold text-gray-700">#{{ $selectedListing->userSticker->sticker->number }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                #{{ $selectedListing->userSticker->sticker->number }} - {{ $selectedListing->userSticker->sticker->name }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                De: {{ $selectedListing->user->name }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- What they want --}}
                @if ($selectedListing->wantedSticker)
                    <div class="mb-4 rounded-lg bg-amber-50 p-3 dark:bg-amber-900/20">
                        <p class="text-sm text-amber-700 dark:text-amber-400">
                            <strong>{{ $selectedListing->user->name }}</strong> busca especificamente:
                            #{{ $selectedListing->wantedSticker->number }} - {{ $selectedListing->wantedSticker->name }}
                        </p>
                    </div>
                @endif

                {{-- Select what you offer --}}
                <div class="mb-6">
                    <h4 class="mb-3 flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                        Selecciona que ofreces a cambio
                        @if (count($offeredStickerIds) > 0)
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-sm text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                {{ count($offeredStickerIds) }} seleccionados
                            </span>
                        @endif
                    </h4>

                    @if (count($myDuplicates) > 0)
                        <div class="max-h-48 overflow-y-auto rounded-lg border-2 border-dashed border-amber-300 bg-amber-50/50 p-3 dark:border-amber-700 dark:bg-amber-900/10">
                            <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6">
                                @foreach ($myDuplicates as $sticker)
                                    <button
                                        type="button"
                                        wire:click="toggleOfferedSticker({{ $sticker['sticker_id'] }})"
                                        class="group relative aspect-[3/4] rounded-lg shadow-md transition-all duration-200 hover:scale-105 hover:shadow-lg {{ in_array($sticker['sticker_id'], $offeredStickerIds) ? 'ring-4 ring-amber-500' : '' }} {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}"
                                    >
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
                                        <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                            #{{ $sticker['number'] }}
                                        </div>
                                        @if (in_array($sticker['sticker_id'], $offeredStickerIds))
                                            <div class="absolute inset-0 flex items-center justify-center rounded-lg bg-amber-500/30">
                                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 text-white">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-gray-600">
                            <p class="text-gray-500 dark:text-gray-400">No tienes cromos disponibles para ofrecer</p>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <button
                        type="button"
                        wire:click="closeTradeModal"
                        class="flex-1 rounded-lg bg-gray-200 px-4 py-3 font-semibold text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="initiateTradeFromListing"
                        wire:loading.attr="disabled"
                        @if (count($offeredStickerIds) === 0) disabled @endif
                        class="flex-1 rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="initiateTradeFromListing">
                            Enviar Propuesta
                        </span>
                        <span wire:loading wire:target="initiateTradeFromListing" class="flex items-center justify-center gap-2">
                            <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enviando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Success Modal --}}
    @if ($showSuccessModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <div class="mb-4 flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                        <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <h3 class="mb-2 text-center text-xl font-semibold text-gray-900 dark:text-white">
                    Éxito
                </h3>
                <p class="mb-6 text-center text-gray-600 dark:text-gray-400">
                    {{ $successMessage }}
                </p>
                <button
                    type="button"
                    wire:click="closeSuccessModal"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700"
                >
                    Continuar
                </button>
            </div>
        </div>
    @endif

    <style>
        .market-grid::-webkit-scrollbar {
            width: 6px;
        }

        .market-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .market-grid::-webkit-scrollbar-thumb {
            background-color: rgba(16, 185, 129, 0.4);
            border-radius: 3px;
        }

        .market-grid::-webkit-scrollbar-thumb:hover {
            background-color: rgba(16, 185, 129, 0.6);
        }

        .sticker-shiny {
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 50%, #fef3c7 100%);
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
    </style>
</div>
