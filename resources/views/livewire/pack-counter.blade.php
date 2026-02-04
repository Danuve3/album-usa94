<div
    wire:key="header-pack-counter-{{ $secondsUntilNextPack }}"
    class="flex cursor-pointer items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 transition-colors hover:bg-amber-100 dark:bg-amber-900/30 dark:hover:bg-amber-900/50"
    wire:poll.30s="refresh"
    x-data="headerPackCountdown({{ $secondsUntilNextPack }})"
    x-init="init()"
>
    <a href="{{ route('album') }}#pack-pile" class="flex items-center gap-1.5">
        <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
            {{ $unopenedCount }}
        </span>
        <span class="hidden text-xs text-amber-600 sm:inline dark:text-amber-400">
            {{ $unopenedCount === 1 ? 'sobre' : 'sobres' }}
        </span>
    </a>

    {{-- Mini countdown when no packs or showing next delivery --}}
    <span
        x-show="seconds > 0"
        x-on:destroy="destroy()"
        x-cloak
        class="ml-1 hidden items-center gap-1 text-[10px] text-amber-500 sm:flex dark:text-amber-400"
    >
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span x-text="miniDisplay"></span>
    </span>
</div>

<script>
    function headerPackCountdown(initialSeconds) {
        return {
            seconds: initialSeconds,
            miniDisplay: '',
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
                        // Refresh to deliver packs and update counter
                        @this.refresh();
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

                if (h > 0) {
                    this.miniDisplay = `${h}h ${m}m`;
                } else if (m > 0) {
                    this.miniDisplay = `${m}m`;
                } else {
                    this.miniDisplay = `<1m`;
                }
            }
        }
    }
</script>
