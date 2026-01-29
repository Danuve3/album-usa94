<div class="flex flex-col items-center" wire:poll.30s="refreshCount">
    @if ($unopenedCount > 0)
        <div class="relative mb-4">
            {{-- Pack pile visual --}}
            <button
                wire:click="openPack"
                wire:loading.attr="disabled"
                wire:loading.class="cursor-wait"
                class="group relative focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 rounded-lg disabled:opacity-75"
                @if ($isOpening) disabled @endif
            >
                {{-- Stacked packs effect --}}
                @for ($i = min($unopenedCount - 1, 4); $i >= 0; $i--)
                    <div
                        class="absolute inset-0 rounded shadow-lg transition-transform duration-200 overflow-hidden"
                        style="transform: translate({{ $i * 3 }}px, {{ $i * 3 }}px); z-index: {{ 5 - $i }};"
                    >
                        <img src="{{ asset('images/packs/pack.webp') }}" alt="Sobre USA 94" class="h-full w-full object-contain">
                    </div>
                @endfor

                {{-- Top pack --}}
                <div class="relative z-10 w-32 aspect-[353/285] rounded shadow-xl transition-all duration-200 group-hover:scale-105 group-hover:shadow-2xl group-active:scale-95 overflow-hidden">
                    <img src="{{ asset('images/packs/pack.webp') }}" alt="Sobre USA 94" class="h-full w-full object-contain">
                    {{-- Open text overlay --}}
                    <div class="absolute inset-0 flex items-end justify-center pb-2 bg-gradient-to-t from-black/50 to-transparent">
                        <div class="text-xs font-semibold text-white drop-shadow-md">
                            <span wire:loading.remove wire:target="openPack">Abrir</span>
                            <span wire:loading wire:target="openPack">...</span>
                        </div>
                    </div>
                </div>
            </button>
        </div>

        {{-- Pack counter --}}
        <div class="text-center">
            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $unopenedCount }}</span>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $unopenedCount === 1 ? 'sobre disponible' : 'sobres disponibles' }}
            </p>
        </div>

    @else
        {{-- No packs available --}}
        <div class="flex flex-col items-center py-8">
            <div class="mb-4 flex w-32 aspect-[353/285] items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
                <svg class="h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <p class="text-center text-gray-500 dark:text-gray-400">
                No tienes sobres disponibles
            </p>
            <p class="mt-1 text-center text-sm text-gray-400 dark:text-gray-500">
                Vuelve mañana para recibir más sobres
            </p>
        </div>
    @endif

    {{-- Sticker Reveal Modal --}}
    @if ($showRevealModal && count($lastOpenedStickers) > 0)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
            x-data="stickerRevealer()"
            x-init="init()"
        >
            <div class="w-full max-w-lg">
                {{-- Title --}}
                <h3 class="mb-6 text-center text-xl font-bold text-white drop-shadow-lg">
                    <span x-show="!allRevealed" x-cloak>Revelando cromos...</span>
                    <span x-show="allRevealed" x-cloak>¡Cromos obtenidos!</span>
                </h3>

                {{-- Stickers Grid --}}
                <div class="grid grid-cols-5 gap-3 mb-6">
                    @foreach ($lastOpenedStickers as $index => $sticker)
                        <div
                            class="sticker-card aspect-[3/4] perspective-1000"
                            style="animation-delay: {{ 0.3 + ($index * 0.5) }}s;"
                            x-data="{ revealed: false }"
                            x-init="$watch('$wire.revealedCount', value => { if (value > {{ $index }}) revealed = true })"
                        >
                            <div
                                class="relative w-full h-full transition-transform duration-700 ease-out transform-style-preserve-3d"
                                :class="{ 'rotate-y-180': revealed }"
                            >
                                {{-- Card Back (unrevealed) --}}
                                <div class="absolute inset-0 w-full h-full backface-hidden rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg flex items-center justify-center border-2 border-emerald-400/50">
                                    <div class="text-white/60 text-3xl font-bold">?</div>
                                </div>

                                {{-- Card Front (revealed) --}}
                                <div
                                    class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 shadow-lg overflow-hidden
                                    {{ $sticker['rarity'] === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}"
                                >
                                    @if (!empty($sticker['image_path']))
                                        <img
                                            src="{{ Storage::url($sticker['image_path']) }}"
                                            alt="{{ $sticker['name'] }}"
                                            class="w-full h-full object-contain"
                                        />
                                    @else
                                        <div class="flex flex-col items-center justify-center h-full p-2">
                                            <span class="text-xl font-bold {{ $sticker['rarity'] === 'shiny' ? 'text-amber-800' : 'text-gray-800 dark:text-white' }}">
                                                {{ $sticker['number'] }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Number Badge --}}
                                    <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[9px] font-bold text-white">
                                        #{{ $sticker['number'] }}
                                    </div>

                                    {{-- Shiny Badge --}}
                                    @if ($sticker['rarity'] === 'shiny')
                                        <span class="absolute top-1 left-1 text-[8px] font-bold text-amber-400 drop-shadow-md sticker-shiny-badge">
                                            ✦ Shiny
                                        </span>
                                    @endif

                                    {{-- Duplicate Badge --}}
                                    @if ($sticker['is_duplicate'])
                                        <span class="sticker-duplicate-badge absolute top-1 right-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-md">
                                            REPE
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Progress indicator --}}
                <div class="flex justify-center gap-2 mb-4">
                    @foreach ($lastOpenedStickers as $index => $sticker)
                        <div
                            class="w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $sticker['rarity'] === 'shiny' ? 'shiny-dot' : '' }}"
                            :class="$wire.revealedCount > {{ $index }}
                                ? '{{ $sticker['rarity'] === 'shiny' ? 'bg-yellow-400 shadow-lg shadow-yellow-400/50' : 'bg-emerald-400' }}'
                                : 'bg-white/30'"
                        ></div>
                    @endforeach
                </div>

                {{-- Info text --}}
                <p class="text-center text-sm text-white/60 mb-4" x-show="allRevealed" x-cloak>
                    Los cromos se han añadido a tu pila de sin pegar
                </p>

                {{-- Actions --}}
                <div class="flex flex-col gap-3">
                    {{-- Reveal All Button (hidden when all revealed) --}}
                    <button
                        x-show="!allRevealed"
                        x-cloak
                        wire:click="revealAllStickers"
                        class="w-full rounded-lg bg-white/10 backdrop-blur px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white/20"
                    >
                        Revelar todos
                    </button>

                    {{-- Continue Button (always visible) --}}
                    <button
                        wire:click="finishReveal"
                        class="w-full rounded-lg bg-emerald-500 px-4 py-3 font-semibold text-white transition-all hover:bg-emerald-600 hover:scale-[1.02] shadow-lg shadow-emerald-500/30"
                    >
                        Continuar
                    </button>
                </div>
            </div>
        </div>

        <script>
            function stickerRevealer() {
                return {
                    allRevealed: false,
                    totalStickers: {{ count($lastOpenedStickers) }},
                    revealInterval: null,

                    init() {
                        this.startAutoReveal();
                        this.$watch('$wire.revealedCount', (value) => {
                            this.allRevealed = value >= this.totalStickers;
                            if (this.allRevealed && this.revealInterval) {
                                clearInterval(this.revealInterval);
                            }
                        });
                    },

                    startAutoReveal() {
                        // Initial delay before first reveal (300ms)
                        setTimeout(() => {
                            @this.revealNextSticker();

                            // Then reveal one sticker every 500ms (0.5s delay as per requirements)
                            this.revealInterval = setInterval(() => {
                                if (@this.revealedCount < this.totalStickers) {
                                    @this.revealNextSticker();
                                } else {
                                    clearInterval(this.revealInterval);
                                }
                            }, 500);
                        }, 300);
                    }
                }
            }
        </script>
    @endif
</div>
