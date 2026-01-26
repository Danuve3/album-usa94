<div class="flex flex-col items-center" wire:poll.30s="refreshCount">
    @if ($unopenedCount > 0)
        <div class="relative mb-4">
            {{-- Pack pile visual --}}
            <button
                wire:click="startOpening"
                wire:loading.attr="disabled"
                wire:loading.class="cursor-wait"
                class="group relative focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 rounded-lg disabled:opacity-75"
                @if ($isOpening) disabled @endif
            >
                {{-- Stacked packs effect --}}
                @for ($i = min($unopenedCount - 1, 4); $i >= 0; $i--)
                    <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg transition-transform duration-200"
                        style="transform: translate({{ $i * 3 }}px, {{ $i * 3 }}px); z-index: {{ 5 - $i }};"
                    ></div>
                @endfor

                {{-- Top pack --}}
                <div class="relative z-10 flex h-40 w-28 flex-col items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-xl transition-all duration-200 group-hover:scale-105 group-hover:shadow-2xl group-active:scale-95">
                    {{-- USA 94 branding --}}
                    <div class="mb-2 text-xs font-bold tracking-wider text-white/80">
                        USA 94
                    </div>

                    {{-- Pack icon --}}
                    <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>

                    {{-- Open text --}}
                    <div class="mt-2 text-xs font-semibold text-white">
                        <span wire:loading.remove wire:target="startOpening">Abrir</span>
                        <span wire:loading wire:target="startOpening">...</span>
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
            <div class="mb-4 flex h-40 w-28 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
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

    {{-- Pack Opening Modal with drag interaction --}}
    @if ($showOpeningModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
            x-data="packOpener()"
            x-init="init()"
            @keydown.escape.window="cancelIfNotTearing()"
        >
            {{-- Instruction text --}}
            <div
                class="absolute top-8 left-0 right-0 text-center transition-opacity duration-300"
                :class="{ 'opacity-0': isTearing }"
            >
                <p class="text-white text-lg font-medium drop-shadow-lg">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-5 h-5 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        Arrastra hacia abajo para abrir
                    </span>
                </p>
            </div>

            {{-- Pack container --}}
            <div class="relative" :class="{ 'pointer-events-none': isTearing }">
                {{-- Tear progress indicator --}}
                <div
                    class="absolute left-1/2 top-0 bottom-0 w-1 -translate-x-1/2 overflow-hidden z-20"
                    x-show="dragProgress > 0 && !isTearing"
                    x-transition
                >
                    <div
                        class="w-full bg-gradient-to-b from-white/80 via-yellow-300 to-white/80"
                        :style="{ height: (dragProgress * 100) + '%' }"
                    ></div>
                </div>

                {{-- Left half of pack --}}
                <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-xl overflow-hidden"
                    :class="{
                        'pack-shake': isDragging && dragProgress > 0.3 && dragProgress < 1,
                        'pack-tear-left': isTearing
                    }"
                    x-show="isTearing"
                    style="clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);"
                >
                    <div class="flex h-48 w-32 flex-col items-center justify-center">
                        <div class="mb-2 text-xs font-bold tracking-wider text-white/80">USA 94</div>
                        <svg class="h-14 w-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Right half of pack --}}
                <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-xl overflow-hidden"
                    :class="{
                        'pack-shake': isDragging && dragProgress > 0.3 && dragProgress < 1,
                        'pack-tear-right': isTearing
                    }"
                    x-show="isTearing"
                    style="clip-path: polygon(50% 0, 100% 0, 100% 100%, 50% 100%);"
                >
                    <div class="flex h-48 w-32 flex-col items-center justify-center">
                        <div class="mb-2 text-xs font-bold tracking-wider text-white/80">USA 94</div>
                        <svg class="h-14 w-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Main draggable pack (before tearing) --}}
                <div
                    class="relative cursor-grab active:cursor-grabbing select-none touch-none"
                    :class="{
                        'pack-shake': isDragging && dragProgress > 0.3 && dragProgress < 1,
                        'opacity-0': isTearing
                    }"
                    :style="{ transform: 'translateY(' + dragY + 'px)' }"
                    @mousedown="startDrag($event)"
                    @touchstart="startDrag($event)"
                    x-ref="pack"
                >
                    <div class="flex h-48 w-32 flex-col items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-2xl transition-shadow"
                         :class="{ 'shadow-emerald-500/50': isDragging }">
                        {{-- USA 94 branding --}}
                        <div class="mb-2 text-xs font-bold tracking-wider text-white/80">
                            USA 94
                        </div>

                        {{-- Pack icon --}}
                        <svg class="h-14 w-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>

                        {{-- Progress indicator --}}
                        <div class="mt-3 w-20 h-1.5 bg-white/30 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-white rounded-full transition-all duration-75"
                                :style="{ width: (dragProgress * 100) + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Close button (only when not tearing) --}}
            <button
                class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors"
                @click="cancelIfNotTearing()"
                x-show="!isTearing"
            >
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <script>
            function packOpener() {
                return {
                    isDragging: false,
                    startY: 0,
                    dragY: 0,
                    dragProgress: 0,
                    isTearing: false,
                    tearThreshold: 120,

                    init() {
                        this.bindEvents();
                    },

                    bindEvents() {
                        window.addEventListener('mousemove', (e) => this.onDrag(e));
                        window.addEventListener('mouseup', () => this.endDrag());
                        window.addEventListener('touchmove', (e) => this.onDrag(e), { passive: false });
                        window.addEventListener('touchend', () => this.endDrag());
                    },

                    startDrag(e) {
                        if (this.isTearing) return;
                        this.isDragging = true;
                        this.startY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
                    },

                    onDrag(e) {
                        if (!this.isDragging || this.isTearing) return;

                        if (e.type === 'touchmove') {
                            e.preventDefault();
                        }

                        const currentY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
                        const deltaY = currentY - this.startY;

                        // Only allow downward drag
                        this.dragY = Math.max(0, Math.min(deltaY, this.tearThreshold + 30));
                        this.dragProgress = Math.min(this.dragY / this.tearThreshold, 1);

                        // Trigger tear when threshold reached
                        if (this.dragProgress >= 1 && !this.isTearing) {
                            this.triggerTear();
                        }
                    },

                    endDrag() {
                        if (this.isTearing) return;

                        this.isDragging = false;

                        // Snap back if not torn
                        if (this.dragProgress < 1) {
                            this.dragY = 0;
                            this.dragProgress = 0;
                        }
                    },

                    triggerTear() {
                        this.isTearing = true;
                        this.isDragging = false;

                        // Call Livewire to open the pack
                        @this.tearPack();

                        // Wait for tear animation then show stickers
                        setTimeout(() => {
                            @this.finishOpening();
                        }, 800);
                    },

                    cancelIfNotTearing() {
                        if (!this.isTearing) {
                            @this.cancelOpening();
                        }
                    }
                }
            }
        </script>
    @endif

    {{-- Last opened stickers modal --}}
    @if (count($lastOpenedStickers) > 0 && !$showOpeningModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="clearLastOpened">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 stickers-reveal">
                <h3 class="mb-4 text-center text-lg font-semibold text-gray-900 dark:text-white">
                    ¡Cromos obtenidos!
                </h3>

                <div class="grid grid-cols-5 gap-2 stickers-reveal">
                    @foreach ($lastOpenedStickers as $sticker)
                        <div class="flex flex-col items-center rounded-lg p-2 {{ $sticker['rarity'] === 'shiny' ? 'bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30' : 'bg-gray-100 dark:bg-gray-700' }}">
                            <span class="text-lg font-bold {{ $sticker['rarity'] === 'shiny' ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $sticker['number'] }}
                            </span>
                            @if ($sticker['rarity'] === 'shiny')
                                <span class="text-xs text-yellow-600 dark:text-yellow-400">Shiny</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button
                    wire:click="clearLastOpened"
                    class="mt-6 w-full rounded-lg bg-emerald-500 px-4 py-2 font-medium text-white transition-colors hover:bg-emerald-600"
                >
                    Continuar
                </button>
            </div>
        </div>
    @endif
</div>
