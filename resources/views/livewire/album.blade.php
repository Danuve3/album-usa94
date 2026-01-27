<div
    class="flex flex-col items-center"
    x-data="albumViewer()"
    x-init="init()"
    @album-go-to-page.window="goToPage($event.detail.page)"
>
    {{-- Album Container --}}
    <div class="relative w-full max-w-4xl">
        {{-- Page Indicator --}}
        <div class="mb-4 text-center">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                Página <span x-text="currentPage + 1">{{ $currentPage + 1 }}</span> de {{ $totalPages }}
            </span>
        </div>

        {{-- Album Book --}}
        <div class="relative mx-auto" style="max-width: 800px;">
            {{-- Navigation: Left Corner (Previous) --}}
            <button
                @click="flipPrev()"
                class="absolute left-0 top-0 bottom-0 z-20 w-16 cursor-pointer opacity-0 hover:opacity-100 transition-opacity duration-200 flex items-center justify-start pl-2"
                :class="{ 'pointer-events-none': currentPage === 0 }"
                x-show="currentPage > 0"
            >
                <div class="bg-black/30 hover:bg-black/50 rounded-full p-2 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
            </button>

            {{-- Navigation: Right Corner (Next) --}}
            <button
                @click="flipNext()"
                class="absolute right-0 top-0 bottom-0 z-20 w-16 cursor-pointer opacity-0 hover:opacity-100 transition-opacity duration-200 flex items-center justify-end pr-2"
                :class="{ 'pointer-events-none': currentPage >= totalPages - 1 }"
                x-show="currentPage < totalPages - 1"
            >
                <div class="bg-black/30 hover:bg-black/50 rounded-full p-2 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

            {{-- Page Flip Container --}}
            <div
                x-ref="albumContainer"
                class="album-container bg-amber-900/20 rounded-lg shadow-2xl overflow-hidden"
            >
                @if ($totalPages > 0)
                    @foreach ($pages as $index => $page)
                        <div class="page bg-amber-50 dark:bg-amber-100" data-page-number="{{ $page['number'] }}">
                            <div class="page-content relative w-full h-full flex items-center justify-center">
                                @if ($page['image_path'])
                                    <img
                                        src="{{ Storage::url($page['image_path']) }}"
                                        alt="Página {{ $page['number'] }}"
                                        class="max-w-full max-h-full object-contain"
                                        loading="lazy"
                                    />
                                @else
                                    <div class="flex flex-col items-center justify-center text-amber-800/50">
                                        <svg class="w-16 h-16 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm">Página {{ $page['number'] }}</span>
                                    </div>
                                @endif

                                {{-- Page Number Overlay --}}
                                <div class="absolute bottom-2 right-2 bg-black/20 text-white/80 text-xs px-2 py-1 rounded">
                                    {{ $page['number'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Empty Album State --}}
                    <div class="page bg-amber-50 dark:bg-amber-100">
                        <div class="page-content flex items-center justify-center h-full">
                            <div class="text-center text-amber-800/50 p-8">
                                <svg class="w-20 h-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-lg font-medium">Álbum vacío</p>
                                <p class="text-sm mt-1">Las páginas se cargarán aquí</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Navigation Controls --}}
        <div class="mt-6 flex items-center justify-center gap-4">
            {{-- First Page Button --}}
            <button
                wire:click="goToFirstPage"
                class="flex items-center gap-1 rounded-lg bg-gray-200 dark:bg-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="currentPage === 0"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                <span class="hidden sm:inline">Primera</span>
            </button>

            {{-- Previous Page Button --}}
            <button
                wire:click="previousPage"
                class="flex items-center gap-1 rounded-lg bg-gray-200 dark:bg-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="currentPage === 0"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="hidden sm:inline">Anterior</span>
            </button>

            {{-- Page Indicator (Mobile) --}}
            <div class="px-4 py-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 font-medium text-sm">
                <span x-text="currentPage + 1">{{ $currentPage + 1 }}</span> / {{ $totalPages }}
            </div>

            {{-- Next Page Button --}}
            <button
                wire:click="nextPage"
                class="flex items-center gap-1 rounded-lg bg-gray-200 dark:bg-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="currentPage >= totalPages - 1"
            >
                <span class="hidden sm:inline">Siguiente</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Last Page Button --}}
            <button
                wire:click="goToLastPage"
                class="flex items-center gap-1 rounded-lg bg-gray-200 dark:bg-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="currentPage >= totalPages - 1"
            >
                <span class="hidden sm:inline">Última</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- Swipe Instructions (Mobile) --}}
        <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400 sm:hidden">
            Desliza o toca las esquinas para pasar página
        </p>
    </div>

    <script>
        function albumViewer() {
            return {
                pageFlip: null,
                currentPage: @entangle('currentPage'),
                totalPages: {{ $totalPages }},

                init() {
                    this.$nextTick(() => {
                        this.initPageFlip();
                    });
                },

                initPageFlip() {
                    const container = this.$refs.albumContainer;
                    if (!container || typeof window.AlbumPageFlip === 'undefined') {
                        console.warn('PageFlip: Container or library not available');
                        return;
                    }

                    const pages = container.querySelectorAll('.page');
                    if (pages.length === 0) {
                        return;
                    }

                    this.pageFlip = new window.AlbumPageFlip(container, {
                        width: 400,
                        height: 500,
                        size: 'stretch',
                        minWidth: 280,
                        maxWidth: 600,
                        minHeight: 350,
                        maxHeight: 750,
                        drawShadow: true,
                        flippingTime: 800,
                        usePortrait: true,
                        autoSize: true,
                        maxShadowOpacity: 0.5,
                        showCover: false,
                        mobileScrollSupport: true,
                        swipeDistance: 30,
                        clickEventForward: true,
                        useMouseEvents: true,
                        showPageCorners: true,
                        disableFlipByClick: false
                    });

                    // Listen for page flip events
                    this.pageFlip.on('flip', (data) => {
                        this.currentPage = data.page;
                        @this.pageFlipped(data.page);
                    });
                },

                flipNext() {
                    if (this.pageFlip) {
                        this.pageFlip.flipNext();
                    }
                },

                flipPrev() {
                    if (this.pageFlip) {
                        this.pageFlip.flipPrev();
                    }
                },

                goToPage(pageIndex) {
                    if (this.pageFlip) {
                        this.pageFlip.turnToPage(pageIndex);
                    }
                },

                destroy() {
                    if (this.pageFlip) {
                        this.pageFlip.destroy();
                        this.pageFlip = null;
                    }
                }
            }
        }
    </script>

    <style>
        .album-container {
            width: 100%;
            aspect-ratio: 4 / 5;
            position: relative;
        }

        .album-container .page {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.1);
        }

        .album-container .page-content {
            padding: 1rem;
        }

        /* Page flip library overrides */
        .stf__parent {
            margin: 0 auto;
        }

        .stf__wrapper {
            margin: 0 auto;
        }

        /* Dark mode adjustments */
        .dark .album-container .page {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
    </style>
</div>
