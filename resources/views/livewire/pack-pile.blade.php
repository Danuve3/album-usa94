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
                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <span class="text-sm font-semibold text-white drop-shadow-md" wire:loading.remove wire:target="openPack">Abrir sobre</span>
                        <span class="text-sm font-semibold text-white drop-shadow-md" wire:loading wire:target="openPack">Abriendo...</span>
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 overflow-hidden"
            x-data="{
                phase: 'enter',
                tearProgress: 0,

                init() {
                    // enter(0-300) → tension(300-900) → tearing(900-1700) → open(1700-2700) → fadeout(2700-3100) → Livewire
                    setTimeout(() => this.phase = 'tension', 300);
                    setTimeout(() => {
                        this.phase = 'tearing';
                        this.animateTear();
                    }, 900);
                },

                animateTear() {
                    const start = performance.now();
                    const duration = 800;
                    const step = (now) => {
                        let t = Math.min((now - start) / duration, 1);
                        // Quadratic ease-in: slow start (resistance) then accelerates (rip)
                        this.tearProgress = t * t * 100;
                        if (t < 1) {
                            requestAnimationFrame(step);
                        } else {
                            this.tearProgress = 100;
                            this.phase = 'open';
                            setTimeout(() => {
                                this.phase = 'fadeout';
                                setTimeout(() => $wire.finishRipAnimation(), 400);
                            }, 1000);
                        }
                    };
                    requestAnimationFrame(step);
                }
            }"
        >
            <div class="relative">
                {{-- Outer wrapper: scale & opacity transitions + micro-zoom --}}
                <div
                    class="transition-[transform,opacity]"
                    :class="{
                        'scale-0 opacity-0 duration-300': phase === 'enter',
                        'scale-100 opacity-100 duration-300': phase === 'tension',
                        'scale-[1.03] opacity-100 duration-500': phase === 'tearing',
                        'scale-[1.05] opacity-100 duration-700': phase === 'open',
                        'scale-95 opacity-0 duration-400': phase === 'fadeout'
                    }"
                >
                    {{-- Inner wrapper: tension shake --}}
                    <div :class="{ 'animate-pack-tension': phase === 'tension' }">
                        {{-- Pack container --}}
                        <div class="relative w-72 aspect-[353/285]">

                            {{-- L1: Pack body with torn top edge --}}
                            <img
                                src="{{ asset('images/packs/pack.webp') }}"
                                alt=""
                                class="absolute inset-0 w-full h-full object-contain"
                                style="clip-path: polygon(0% 23%, 5% 25%, 10% 22%, 15% 26%, 20% 23%, 25% 24%, 30% 25%, 35% 23%, 40% 27%, 45% 22%, 50% 25%, 55% 21%, 60% 27%, 65% 24%, 70% 22%, 75% 26%, 80% 23%, 85% 28%, 90% 21%, 95% 25%, 100% 23%, 100% 100%, 0% 100%);"
                            >

                            {{-- L2: Golden glow in tear gap --}}
                            <div
                                class="absolute left-0 right-0 pointer-events-none transition-opacity duration-500"
                                style="top: 16%; height: 16%;"
                                :class="{
                                    'opacity-0': phase === 'enter' || phase === 'tension' || phase === 'fadeout',
                                    'opacity-80': phase === 'tearing',
                                    'opacity-100': phase === 'open'
                                }"
                            >
                                <div
                                    class="w-full h-full"
                                    style="background: radial-gradient(ellipse 100% 100% at center, rgba(251,191,36,0.9) 0%, rgba(245,158,11,0.5) 40%, transparent 80%);"
                                ></div>
                            </div>

                            {{-- L3: Top flap with torn bottom edge (lifts in open phase) --}}
                            <div
                                class="absolute inset-0"
                                style="transform-origin: center 22%; transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);"
                                :style="(phase === 'open' || phase === 'fadeout')
                                    ? 'transform: translateY(-50px) rotate(-12deg);'
                                    : 'transform: none;'"
                            >
                                <img
                                    src="{{ asset('images/packs/pack.webp') }}"
                                    alt=""
                                    class="w-full h-full object-contain"
                                    style="clip-path: polygon(0% 0%, 100% 0%, 100% 20%, 95% 22%, 90% 18%, 85% 25%, 80% 20%, 75% 23%, 70% 19%, 65% 21%, 60% 24%, 55% 18%, 50% 22%, 45% 19%, 40% 24%, 35% 20%, 30% 22%, 25% 21%, 20% 20%, 15% 23%, 10% 19%, 5% 22%, 0% 20%);"
                                >
                            </div>

                            {{-- L4: Intact overlay (reveals tear from left to right) --}}
                            <img
                                src="{{ asset('images/packs/pack.webp') }}"
                                alt=""
                                class="absolute inset-0 w-full h-full object-contain"
                                :style="'clip-path: inset(0 0 0 ' + tearProgress + '%);'"
                            >

                            {{-- L5: Foil shimmer overlay --}}
                            <div
                                class="absolute inset-0 pointer-events-none rounded"
                                style="mix-blend-mode: overlay;"
                                :class="{
                                    'pack-foil-shimmer': phase === 'tension' || phase === 'tearing' || phase === 'open',
                                    'opacity-0': phase === 'enter' || phase === 'fadeout'
                                }"
                            ></div>

                            {{-- L6: Tear spark at rip point --}}
                            <div
                                class="absolute pointer-events-none w-3 h-3 -ml-1.5 -mt-1.5 rounded-full bg-white"
                                x-show="phase === 'tearing'"
                                x-transition:leave="transition-opacity duration-200"
                                :style="'left:' + tearProgress + '%; top: 21%; box-shadow: 0 0 10px 4px rgba(251,191,36,0.9), 0 0 20px 8px rgba(251,191,36,0.4);'"
                            ></div>
                        </div>
                    </div>
                </div>

                {{-- Opening text --}}
                <p
                    class="absolute -bottom-10 left-1/2 -translate-x-1/2 text-white font-bold text-lg whitespace-nowrap transition-opacity duration-300"
                    :class="{ 'opacity-100': phase === 'tension', 'opacity-0': phase !== 'tension' }"
                >
                    Abriendo sobre...
                </p>
            </div>
        </div>
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
            <div class="w-full max-w-6xl rounded-2xl bg-gray-900/80 backdrop-blur-sm p-8 reveal-modal-enter">
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
                <div class="flex justify-center items-center gap-3 mb-6">
                    @foreach ($lastOpenedStickers as $index => $sticker)
                        @php
                            $isHorizontal = ($sticker['width'] ?? 0) > ($sticker['height'] ?? 0);
                            $backImage = $isHorizontal ? 'sticker_back_horizontal.webp' : 'sticker_back.webp';
                        @endphp
                        <div
                            class="sticker-card perspective-1000 cursor-pointer {{ $isHorizontal ? 'w-[200px] aspect-[4/3]' : 'w-[150px] aspect-[3/4]' }}"
                            x-on:click="revealSticker({{ $index }})"
                        >
                            <div
                                class="relative w-full h-full transition-transform duration-700 ease-out transform-style-preserve-3d"
                                x-bind:class="{ 'rotate-y-180': revealed[{{ $index }}] }"
                            >
                                <div class="sticker-back-container absolute inset-0 w-full h-full backface-hidden shadow-lg overflow-hidden hover:scale-105 transition-transform">
                                    <img src="{{ asset('images/stickers/' . $backImage) }}" alt="Reverso" class="w-full h-full object-cover">
                                    @php
                                        $backNumConfig = $isHorizontal ? $backNumberHorizontal : $backNumberVertical;
                                    @endphp
                                    @if ($backNumConfig['enabled'])
                                        <span class="sticker-back-number"
                                              style="left: {{ $backNumConfig['position_x'] }}%; top: {{ $backNumConfig['position_y'] }}%; font-size: {{ $backNumConfig['font_size'] }}cqi; font-weight: {{ $backNumConfig['font_weight'] }}; font-family: {{ $backNumConfig['font_family'] }}; color: {{ $backNumConfig['color'] }};">
                                            {{ $sticker['number'] }}
                                        </span>
                                    @endif
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
                <div class="flex gap-3">
                    <button
                        x-on:click="revealAll()"
                        x-show="!allRevealed"
                        class="flex-1 cursor-pointer rounded-lg bg-white/15 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/25"
                    >
                        Revelar todos
                    </button>

                    <button
                        wire:click="finishReveal"
                        class="flex-1 cursor-pointer rounded-lg bg-emerald-500 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-emerald-600"
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
