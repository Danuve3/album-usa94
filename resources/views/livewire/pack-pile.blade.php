<div class="flex flex-col items-center" wire:poll.30s="refreshCount">
    @if ($unopenedCount > 0)
        <div class="relative mb-4">
            {{-- Pack pile visual --}}
            <button
                wire:click="openPack"
                wire:loading.attr="disabled"
                wire:loading.class="cursor-wait"
                class="group relative cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 rounded-lg disabled:opacity-75"
                @if ($isOpening) disabled @endif
            >
                {{-- Stacked packs effect --}}
                @for ($i = min($unopenedCount - 1, 4); $i >= 0; $i--)
                    <div
                        class="absolute inset-0 rounded shadow-lg transition-transform duration-200 overflow-hidden"
                        style="transform: translate({{ $i * 6 }}px, {{ $i * 6 }}px); z-index: {{ 5 - $i }};"
                    >
                        <img src="{{ asset('images/packs/pack.webp') }}" alt="Sobre USA 94" class="h-full w-full object-contain">
                    </div>
                @endfor

                {{-- Top pack --}}
                <div class="relative z-10 w-64 aspect-[353/285] rounded shadow-xl transition-all duration-200 group-hover:scale-105 group-hover:shadow-2xl group-active:scale-95 overflow-hidden">
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

        {{-- Next pack countdown (shown below counter when packs available) --}}
        @if ($secondsUntilNextPack > 0)
            <div
                wire:key="pack-countdown-available-{{ $secondsUntilNextPack }}"
                class="mt-3 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400"
                x-data="packCountdown({{ $secondsUntilNextPack }})"
                x-init="init()"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Próximo sobre en <span x-text="display" class="font-medium"></span></span>
            </div>
        @endif

    @else
        {{-- No packs available --}}
        <div class="flex flex-col items-center py-8">
            <div class="mb-4 flex w-64 aspect-[353/285] items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
                <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <p class="text-center text-gray-500 dark:text-gray-400">
                No tienes sobres disponibles
            </p>

            {{-- Countdown timer when no packs --}}
            @if ($secondsUntilNextPack > 0)
                <div
                    wire:key="pack-countdown-empty-{{ $secondsUntilNextPack }}"
                    class="mt-3 flex flex-col items-center gap-2"
                    x-data="packCountdown({{ $secondsUntilNextPack }})"
                    x-init="init()"
                >
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Próximo sobre en:
                    </p>
                    <div class="flex items-center gap-1 rounded-lg bg-emerald-50 px-4 py-2 dark:bg-emerald-900/30">
                        <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="display" class="font-mono text-lg font-semibold text-emerald-700 dark:text-emerald-300"></span>
                    </div>
                </div>
            @else
                <p class="mt-1 text-center text-sm text-gray-400 dark:text-gray-500">
                    Recarga la página para recibir tu sobre
                </p>
            @endif
        </div>
    @endif

    {{-- Pack Rip Animation --}}
    @if ($showRipAnimation)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
            x-data="{
                phase: 'enter',
                init() {
                    // Phase 1: Pack enters and shakes
                    setTimeout(() => this.phase = 'shake', 300);
                    // Phase 2: Start ripping
                    setTimeout(() => this.phase = 'rip', 1000);
                    // Phase 3: Finish and show stickers
                    setTimeout(() => $wire.finishRipAnimation(), 2200);
                }
            }"
        >
            <div class="relative">
                {{-- Particles/Confetti effect --}}
                <div
                    class="absolute inset-0 pointer-events-none overflow-visible"
                    x-show="phase === 'rip'"
                    x-transition:enter="transition-opacity duration-300"
                >
                    @for ($i = 0; $i < 12; $i++)
                        <div
                            class="absolute w-2 h-3 bg-gradient-to-b from-yellow-300 to-amber-500 rounded-sm"
                            style="
                                left: {{ 45 + ($i % 4) * 10 }}%;
                                top: 20%;
                                animation: particle-{{ $i % 4 }} 1s ease-out forwards;
                                animation-delay: {{ ($i * 0.05) }}s;
                            "
                        ></div>
                    @endfor
                </div>

                {{-- Pack container --}}
                <div
                    class="relative w-72 transition-all duration-300"
                    :class="{
                        'scale-0 opacity-0': phase === 'enter',
                        'scale-100 opacity-100': phase !== 'enter',
                        'animate-pack-shake': phase === 'shake'
                    }"
                    x-init="setTimeout(() => phase = 'visible', 50)"
                >
                    {{-- Top part of pack (rips up) --}}
                    <div
                        class="absolute top-0 left-0 right-0 h-[25%] overflow-hidden z-10 transition-all duration-700 ease-out origin-bottom"
                        :class="{
                            'translate-y-0 rotate-0': phase !== 'rip',
                            '-translate-y-16 -rotate-12 opacity-0': phase === 'rip'
                        }"
                    >
                        <img
                            src="{{ asset('images/packs/pack.webp') }}"
                            alt="Sobre USA 94"
                            class="w-full object-cover object-top"
                            style="clip-path: polygon(0 0, 100% 0, 100% 100%, 85% 95%, 70% 100%, 55% 95%, 40% 100%, 25% 95%, 10% 100%, 0 95%);"
                        >
                    </div>

                    {{-- Main pack body --}}
                    <div class="relative aspect-[353/285] overflow-hidden">
                        <img
                            src="{{ asset('images/packs/pack.webp') }}"
                            alt="Sobre USA 94"
                            class="w-full h-full object-contain"
                        >

                        {{-- Glow effect when ripping --}}
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-yellow-400/0 via-yellow-400/0 to-yellow-400/0 transition-all duration-500"
                            :class="{ 'from-yellow-400/80 via-yellow-400/40 to-transparent': phase === 'rip' }"
                        ></div>

                        {{-- Rip line effect --}}
                        <div
                            class="absolute top-[20%] left-0 right-0 h-1 transition-all duration-300"
                            :class="{
                                'opacity-0 scale-x-0': phase !== 'rip',
                                'opacity-100 scale-x-100 bg-gradient-to-r from-transparent via-white to-transparent shadow-lg shadow-white/50': phase === 'rip'
                            }"
                        ></div>
                    </div>

                    {{-- Stickers peeking out --}}
                    <div
                        class="absolute top-[15%] left-1/2 -translate-x-1/2 flex gap-1 transition-all duration-700 ease-out"
                        :class="{
                            'opacity-0 translate-y-4': phase !== 'rip',
                            'opacity-100 -translate-y-2': phase === 'rip'
                        }"
                    >
                        @for ($i = 0; $i < 3; $i++)
                            <div
                                class="w-8 h-10 rounded-sm shadow-lg transform transition-all duration-500"
                                style="
                                    background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
                                    animation-delay: {{ $i * 0.1 }}s;
                                    transform: rotate({{ ($i - 1) * 8 }}deg);
                                "
                            ></div>
                        @endfor
                    </div>
                </div>

                {{-- Opening text --}}
                <p
                    class="absolute -bottom-12 left-1/2 -translate-x-1/2 text-white font-bold text-lg whitespace-nowrap transition-opacity duration-300"
                    :class="{ 'opacity-100': phase === 'shake', 'opacity-0': phase !== 'shake' }"
                >
                    Abriendo sobre...
                </p>
            </div>
        </div>

        <style>
            @keyframes pack-shake {
                0%, 100% { transform: rotate(0deg) scale(1); }
                10% { transform: rotate(-3deg) scale(1.02); }
                20% { transform: rotate(3deg) scale(1.02); }
                30% { transform: rotate(-3deg) scale(1.03); }
                40% { transform: rotate(3deg) scale(1.03); }
                50% { transform: rotate(-2deg) scale(1.04); }
                60% { transform: rotate(2deg) scale(1.04); }
                70% { transform: rotate(-1deg) scale(1.03); }
                80% { transform: rotate(1deg) scale(1.02); }
                90% { transform: rotate(0deg) scale(1.01); }
            }
            .animate-pack-shake {
                animation: pack-shake 0.7s ease-in-out;
            }
            @keyframes particle-0 {
                0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                100% { transform: translate(-60px, -80px) rotate(180deg); opacity: 0; }
            }
            @keyframes particle-1 {
                0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                100% { transform: translate(-20px, -100px) rotate(-120deg); opacity: 0; }
            }
            @keyframes particle-2 {
                0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                100% { transform: translate(20px, -90px) rotate(150deg); opacity: 0; }
            }
            @keyframes particle-3 {
                0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                100% { transform: translate(60px, -70px) rotate(-180deg); opacity: 0; }
            }
        </style>
    @endif

    {{-- Sticker Reveal Modal --}}
    @if ($showRevealModal && count($lastOpenedStickers) > 0)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
            x-data="{
                revealed: [{{ implode(', ', array_fill(0, count($lastOpenedStickers), 'false')) }}],
                get allRevealed() { return this.revealed.every(r => r); },
                revealSticker(index) { this.revealed[index] = true; },
                revealAll() { this.revealed = this.revealed.map(() => true); }
            }"
        >
            <div class="w-full max-w-4xl">
                {{-- Title --}}
                <h3 class="mb-6 text-center text-xl font-bold text-white drop-shadow-lg">
                    <template x-if="!allRevealed">
                        <span>Toca los cromos para revelarlos</span>
                    </template>
                    <template x-if="allRevealed">
                        <span>¡Cromos obtenidos!</span>
                    </template>
                </h3>

                {{-- Stickers Grid --}}
                <div class="flex justify-center gap-3 mb-6">
                    @foreach ($lastOpenedStickers as $index => $sticker)
                        <div
                            class="sticker-card w-[150px] aspect-[3/4] perspective-1000 cursor-pointer"
                            x-on:click="revealSticker({{ $index }})"
                        >
                            <div
                                class="relative w-full h-full transition-transform duration-700 ease-out transform-style-preserve-3d"
                                x-bind:class="{ 'rotate-y-180': revealed[{{ $index }}] }"
                            >
                                {{-- Card Back (unrevealed) --}}
                                <div class="absolute inset-0 w-full h-full backface-hidden bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg flex items-center justify-center border-2 border-emerald-400/50 hover:scale-105 transition-transform">
                                    <div class="text-white/60 text-3xl font-bold">?</div>
                                </div>

                                {{-- Card Front (revealed) --}}
                                @php
                                    $cardClasses = 'absolute inset-0 w-full h-full backface-hidden rotate-y-180 shadow-lg overflow-hidden';
                                    if ($sticker['rarity'] === 'shiny' && $shinyStyleEnabled) {
                                        $cardClasses .= ' sticker-shiny';
                                    } elseif ($sticker['rarity'] !== 'shiny' && $normalStyleEnabled) {
                                        $cardClasses .= ' bg-white dark:bg-gray-700';
                                    } else {
                                        $cardClasses .= ' bg-transparent';
                                    }
                                @endphp
                                <div class="{{ $cardClasses }}">
                                    @if (!empty($sticker['image_path']))
                                        <img
                                            src="{{ Storage::url($sticker['image_path']) }}"
                                            alt="{{ $sticker['name'] }}"
                                            class="w-full h-full object-contain"
                                        />
                                    @else
                                        <div class="flex flex-col items-center justify-center h-full p-2">
                                            <span class="text-2xl font-bold {{ $sticker['rarity'] === 'shiny' ? 'text-amber-800' : 'text-gray-800 dark:text-white' }}">
                                                {{ $sticker['number'] }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Number Badge --}}
                                    <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                        #{{ $sticker['number'] }}
                                    </div>

                                    {{-- Shiny Badge --}}
                                    @if ($sticker['rarity'] === 'shiny')
                                        <span class="absolute top-1 left-1 text-[9px] font-bold text-amber-400 drop-shadow-md sticker-shiny-badge">
                                            ✦ Shiny
                                        </span>
                                    @endif

                                    {{-- Duplicate Badge --}}
                                    @if ($sticker['is_duplicate'])
                                        <span class="sticker-duplicate-badge absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-md">
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
                            x-bind:class="revealed[{{ $index }}]
                                ? '{{ $sticker['rarity'] === 'shiny' ? 'bg-yellow-400 shadow-lg shadow-yellow-400/50' : 'bg-emerald-400' }}'
                                : 'bg-white/30'"
                        ></div>
                    @endforeach
                </div>

                {{-- Info text --}}
                <template x-if="allRevealed">
                    <p class="text-center text-sm text-white/60 mb-4">
                        Los cromos se han añadido a tu pila de sin pegar
                    </p>
                </template>

                {{-- Actions --}}
                <div class="flex flex-col gap-3">
                    {{-- Reveal All Button (hidden when all revealed) --}}
                    <template x-if="!allRevealed">
                        <button
                            x-on:click="revealAll()"
                            class="w-full cursor-pointer rounded-lg bg-white/10 backdrop-blur px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white/20"
                        >
                            Revelar todos
                        </button>
                    </template>

                    {{-- Continue Button (always visible) --}}
                    <button
                        wire:click="finishReveal"
                        class="w-full cursor-pointer rounded-lg bg-emerald-500 px-4 py-3 font-semibold text-white transition-all hover:bg-emerald-600 hover:scale-[1.02] shadow-lg shadow-emerald-500/30"
                    >
                        Continuar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Pack countdown Alpine component --}}
    <script>
        function packCountdown(initialSeconds) {
            return {
                seconds: initialSeconds,
                display: '',
                interval: null,

                init() {
                    this.updateDisplay();
                    this.startCountdown();
                },

                startCountdown() {
                    // Clear any existing interval to prevent duplicates
                    if (this.interval) {
                        clearInterval(this.interval);
                    }

                    this.interval = setInterval(() => {
                        if (this.seconds > 0) {
                            this.seconds--;
                            this.updateDisplay();
                        } else {
                            clearInterval(this.interval);
                            this.interval = null;
                            // Refresh via Livewire when countdown reaches 0
                            @this.refreshCount();
                        }
                    }, 1000);
                },

                destroy() {
                    if (this.interval) {
                        clearInterval(this.interval);
                        this.interval = null;
                    }
                },

                updateDisplay() {
                    const h = Math.floor(this.seconds / 3600);
                    const m = Math.floor((this.seconds % 3600) / 60);
                    const s = this.seconds % 60;

                    if (h > 0) {
                        this.display = `${h}h ${m}m ${s}s`;
                    } else if (m > 0) {
                        this.display = `${m}m ${s}s`;
                    } else {
                        this.display = `${s}s`;
                    }
                }
            }
        }
    </script>
</div>
