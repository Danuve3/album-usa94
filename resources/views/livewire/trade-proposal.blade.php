<div class="flex flex-col gap-6">
    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- User Search Section --}}
    <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
            Buscar Usuario
        </h2>

        @if (!$selectedUser)
            {{-- Search Input --}}
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="userSearch"
                    placeholder="Buscar por nombre o email..."
                    class="w-full rounded-lg border border-gray-300 bg-white py-3 pl-10 pr-4 text-gray-900 placeholder-gray-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                />
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- Search Results --}}
            @if ($searchResults->count() > 0)
                <div class="mt-3 max-h-64 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                    @foreach ($searchResults as $user)
                        <button
                            type="button"
                            wire:click="selectUser({{ $user->id }})"
                            class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <span class="text-sm font-medium">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
            @elseif (strlen($userSearch) >= 2)
                <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-4 text-center text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                    No se encontraron usuarios con "{{ $userSearch }}"
                </div>
            @endif
        @else
            {{-- Selected User Card --}}
            <div class="flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-200 text-emerald-700 dark:bg-emerald-800 dark:text-emerald-300">
                        <span class="text-lg font-medium">{{ strtoupper(substr($selectedUser->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedUser->email }}</p>
                    </div>
                </div>
                <button
                    type="button"
                    wire:click="clearSelectedUser"
                    class="rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    Cambiar
                </button>
            </div>
        @endif
    </div>

    {{-- Trade Interface (only shown when user is selected) --}}
    @if ($selectedUser)
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- My Stickers (Offered) --}}
            <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Mis Cromos para Ofrecer
                    </h2>
                    @if (count($offeredStickerIds) > 0)
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            {{ count($offeredStickerIds) }} seleccionados
                        </span>
                    @endif
                </div>

                @if (count($myDuplicates) > 0)
                    <div class="sticker-grid max-h-80 overflow-y-auto rounded-lg border-2 border-dashed border-emerald-300 bg-emerald-50/50 p-3 dark:border-emerald-700 dark:bg-emerald-900/10">
                        <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-4">
                            @foreach ($myDuplicates as $sticker)
                                <button
                                    type="button"
                                    wire:click="toggleOfferedSticker({{ $sticker['sticker_id'] }})"
                                    class="group relative aspect-[3/4] rounded-lg shadow-md transition-all duration-200 hover:scale-105 hover:shadow-lg {{ in_array($sticker['sticker_id'], $offeredStickerIds) ? 'ring-4 ring-emerald-500' : '' }} {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}"
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

                                    {{-- Count Badge --}}
                                    @if ($sticker['count'] > 1)
                                        <div class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-500 px-1 text-[10px] font-bold text-white shadow-md">
                                            x{{ $sticker['count'] }}
                                        </div>
                                    @endif

                                    {{-- Selected Checkmark --}}
                                    @if (in_array($sticker['sticker_id'], $offeredStickerIds))
                                        <div class="absolute inset-0 flex items-center justify-center rounded-lg bg-emerald-500/30">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-white">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Tooltip --}}
                                    <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                        {{ $sticker['name'] }}
                                        <div class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
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
                        <p class="text-gray-500 dark:text-gray-400">No tienes cromos disponibles para intercambiar</p>
                    </div>
                @endif
            </div>

            {{-- Their Stickers (Requested) --}}
            <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Cromos de {{ $selectedUser->name }}
                    </h2>
                    @if (count($requestedStickerIds) > 0)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                            {{ count($requestedStickerIds) }} seleccionados
                        </span>
                    @endif
                </div>

                @if (count($theirDuplicates) > 0)
                    <div class="sticker-grid max-h-80 overflow-y-auto rounded-lg border-2 border-dashed border-amber-300 bg-amber-50/50 p-3 dark:border-amber-700 dark:bg-amber-900/10">
                        <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-4">
                            @foreach ($theirDuplicates as $sticker)
                                <button
                                    type="button"
                                    wire:click="toggleRequestedSticker({{ $sticker['sticker_id'] }})"
                                    class="group relative aspect-[3/4] rounded-lg shadow-md transition-all duration-200 hover:scale-105 hover:shadow-lg {{ in_array($sticker['sticker_id'], $requestedStickerIds) ? 'ring-4 ring-amber-500' : '' }} {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}"
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

                                    {{-- Count Badge --}}
                                    @if ($sticker['count'] > 1)
                                        <div class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-500 px-1 text-[10px] font-bold text-white shadow-md">
                                            x{{ $sticker['count'] }}
                                        </div>
                                    @endif

                                    {{-- Selected Checkmark --}}
                                    @if (in_array($sticker['sticker_id'], $requestedStickerIds))
                                        <div class="absolute inset-0 flex items-center justify-center rounded-lg bg-amber-500/30">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Tooltip --}}
                                    <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                        {{ $sticker['name'] }}
                                        <div class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
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
                        <p class="text-gray-500 dark:text-gray-400">{{ $selectedUser->name }} no tiene cromos disponibles para intercambiar</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Trade Summary and Submit --}}
        <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
                Resumen del Intercambio
            </h2>

            <div class="mb-6 grid gap-4 sm:grid-cols-2">
                {{-- Offered Summary --}}
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <h3 class="mb-2 flex items-center gap-2 font-medium text-emerald-700 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                        Ofreces {{ count($offeredStickerIds) }} cromos
                    </h3>
                    @if (count($offeredStickerIds) > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach ($myDuplicates as $sticker)
                                @if (in_array($sticker['sticker_id'], $offeredStickerIds))
                                    <span class="rounded bg-emerald-200 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200">
                                        #{{ $sticker['number'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-emerald-600 dark:text-emerald-400">Selecciona cromos para ofrecer</p>
                    @endif
                </div>

                {{-- Requested Summary --}}
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                    <h3 class="mb-2 flex items-center gap-2 font-medium text-amber-700 dark:text-amber-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                        Solicitas {{ count($requestedStickerIds) }} cromos
                    </h3>
                    @if (count($requestedStickerIds) > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach ($theirDuplicates as $sticker)
                                @if (in_array($sticker['sticker_id'], $requestedStickerIds))
                                    <span class="rounded bg-amber-200 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-800 dark:text-amber-200">
                                        #{{ $sticker['number'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-amber-600 dark:text-amber-400">Selecciona cromos para solicitar</p>
                    @endif
                </div>
            </div>

            {{-- Submit Button --}}
            <button
                type="button"
                wire:click="sendProposal"
                wire:loading.attr="disabled"
                @if (count($offeredStickerIds) === 0 || count($requestedStickerIds) === 0) disabled @endif
                class="w-full rounded-lg bg-emerald-600 px-6 py-3 text-center font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-800"
            >
                <span wire:loading.remove wire:target="sendProposal">
                    Enviar Propuesta de Intercambio
                </span>
                <span wire:loading wire:target="sendProposal" class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Enviando...
                </span>
            </button>
        </div>
    @else
        {{-- Instructions when no user selected --}}
        <div class="rounded-2xl bg-white p-8 shadow-xl dark:bg-gray-800">
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                    <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">
                    Proponer un Intercambio
                </h3>
                <p class="max-w-md text-gray-600 dark:text-gray-400">
                    Busca un usuario por nombre para ver sus cromos repetidos disponibles y proponer un intercambio directo.
                </p>
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
                    Propuesta Enviada
                </h3>
                <p class="mb-6 text-center text-gray-600 dark:text-gray-400">
                    Tu propuesta de intercambio ha sido enviada a {{ $selectedUser?->name }}. Te notificaremos cuando responda.
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
        .sticker-grid::-webkit-scrollbar {
            width: 6px;
        }

        .sticker-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .sticker-grid::-webkit-scrollbar-thumb {
            background-color: rgba(16, 185, 129, 0.4);
            border-radius: 3px;
        }

        .sticker-grid::-webkit-scrollbar-thumb:hover {
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
