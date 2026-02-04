<div
    class="flex flex-col"
    x-data="albumViewer()"
    x-init="init()"
    @album-go-to-page.window="goToPage($event.detail.page)"
>
    {{-- Album Container --}}
    <div class="relative w-full">
        {{-- Album Book --}}
        <div class="relative w-full">
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
                        <div class="page bg-amber-50 dark:bg-amber-100" data-page-number="{{ $page['number'] }}" data-page-index="{{ $index }}">
                            <div class="page-content relative w-full h-full">
                                @if ($page['image_path'])
                                    <img
                                        src="{{ Storage::url($page['image_path']) }}"
                                        alt="Página {{ $page['number'] }}"
                                        class="absolute inset-0 w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                @else
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-amber-800/50">
                                        <svg class="w-16 h-16 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm">Página {{ $page['number'] }}</span>
                                    </div>
                                @endif

                                {{-- Stickers Layer (all states: glued, available, empty) --}}
                                @if (!empty($page['stickers']))
                                    @foreach ($page['stickers'] as $sticker)
                                        @if ($sticker['status'] === 'glued')
                                            {{-- Glued Sticker --}}
                                            <div
                                                class="sticker-glued absolute"
                                                style="
                                                    left: {{ $sticker['position_x'] }}%;
                                                    top: {{ $sticker['position_y'] }}%;
                                                    width: {{ $sticker['width'] }}%;
                                                    height: {{ $sticker['height'] }}%;
                                                    {{ $sticker['is_horizontal'] ? 'transform: rotate(90deg); transform-origin: center center;' : '' }}
                                                "
                                                data-sticker-id="{{ $sticker['id'] }}"
                                                data-sticker-number="{{ $sticker['number'] }}"
                                            >
                                                @if ($sticker['image_path'])
                                                    <img
                                                        data-src="{{ Storage::url($sticker['image_path']) }}"
                                                        alt="{{ $sticker['name'] }}"
                                                        class="w-full h-full object-contain sticker-image"
                                                    />
                                                @else
                                                    <div class="w-full h-full bg-gray-300 dark:bg-gray-600 rounded flex items-center justify-center text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $sticker['number'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif ($sticker['status'] === 'available')
                                            {{-- Available Sticker (user has it but not glued) --}}
                                            <div
                                                class="sticker-slot sticker-available absolute group"
                                                style="
                                                    left: {{ $sticker['position_x'] }}%;
                                                    top: {{ $sticker['position_y'] }}%;
                                                    width: {{ $sticker['width'] }}%;
                                                    height: {{ $sticker['height'] }}%;
                                                    {{ $sticker['is_horizontal'] ? 'transform: rotate(90deg); transform-origin: center center;' : '' }}
                                                "
                                                data-sticker-id="{{ $sticker['id'] }}"
                                                data-sticker-number="{{ $sticker['number'] }}"
                                                title="{{ $sticker['name'] }}"
                                            >
                                                <div class="w-full h-full rounded border-2 border-dashed border-emerald-500 bg-emerald-500/20 flex flex-col items-center justify-center">
                                                    <span class="text-emerald-700 dark:text-emerald-400 font-bold text-xs sm:text-sm">{{ $sticker['number'] }}</span>
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-emerald-600 dark:text-emerald-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </div>
                                                {{-- Tooltip --}}
                                                <div class="sticker-tooltip opacity-0 group-hover:opacity-100 absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 bg-emerald-800 text-white text-xs rounded whitespace-nowrap z-20 pointer-events-none transition-opacity">
                                                    {{ $sticker['name'] }}
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-emerald-800"></div>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Empty Slot (user doesn't have this sticker) --}}
                                            <div
                                                class="sticker-slot sticker-empty absolute group"
                                                style="
                                                    left: {{ $sticker['position_x'] }}%;
                                                    top: {{ $sticker['position_y'] }}%;
                                                    width: {{ $sticker['width'] }}%;
                                                    height: {{ $sticker['height'] }}%;
                                                    {{ $sticker['is_horizontal'] ? 'transform: rotate(90deg); transform-origin: center center;' : '' }}
                                                "
                                                data-sticker-id="{{ $sticker['id'] }}"
                                                data-sticker-number="{{ $sticker['number'] }}"
                                                title="{{ $sticker['name'] }}"
                                            >
                                                <div class="w-full h-full rounded border border-dashed border-gray-400/50 bg-gray-500/10 flex items-center justify-center">
                                                    <span class="text-gray-500/70 dark:text-gray-400/50 font-medium text-xs sm:text-sm">{{ $sticker['number'] }}</span>
                                                </div>
                                                {{-- Tooltip --}}
                                                <div class="sticker-tooltip opacity-0 group-hover:opacity-100 absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 bg-gray-800 text-white text-xs rounded whitespace-nowrap z-20 pointer-events-none transition-opacity">
                                                    {{ $sticker['name'] }}
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif

                                {{-- Sticker Counter --}}
                                @if ($page['total_count'] > 0)
                                    <div class="absolute top-2 left-2 bg-black/40 text-white text-xs px-2 py-1 rounded z-10 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                                        </svg>
                                        <span>{{ $page['glued_count'] }}/{{ $page['total_count'] }}</span>
                                    </div>
                                @endif

                                {{-- Page Number Overlay --}}
                                <div class="absolute bottom-2 right-2 bg-black/20 text-white/80 text-xs px-2 py-1 rounded z-10">
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
        <div class="mt-3 flex items-center justify-center gap-4">
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
        <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400 sm:hidden">
            Desliza o toca las esquinas para pasar página
        </p>
    </div>

    <script>
        function albumViewer() {
            return {
                pageFlip: null,
                currentPage: @entangle('currentPage'),
                totalPages: {{ $totalPages }},
                loadedPages: new Set(),
                stickerObserver: null,

                init() {
                    this.$nextTick(() => {
                        this.initPageFlip();
                        this.initStickerLazyLoading();
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
                        maxWidth: 1200,
                        minHeight: 350,
                        maxHeight: 1500,
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
                        this.loadStickersForVisiblePages(data.page);
                    });

                    // Load stickers for initial page
                    this.loadStickersForVisiblePages(this.currentPage);
                },

                initStickerLazyLoading() {
                    // Use Intersection Observer for sticker images
                    if ('IntersectionObserver' in window) {
                        this.stickerObserver = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    const img = entry.target;
                                    if (img.dataset.src) {
                                        img.src = img.dataset.src;
                                        img.removeAttribute('data-src');
                                        this.stickerObserver.unobserve(img);
                                    }
                                }
                            });
                        }, {
                            rootMargin: '50px'
                        });
                    }
                },

                loadStickersForVisiblePages(currentPageIndex) {
                    const container = this.$refs.albumContainer;
                    if (!container) return;

                    // Load current page and adjacent pages for smooth flipping
                    const pagesToLoad = [
                        currentPageIndex - 1,
                        currentPageIndex,
                        currentPageIndex + 1
                    ].filter(p => p >= 0 && p < this.totalPages);

                    pagesToLoad.forEach(pageIndex => {
                        if (this.loadedPages.has(pageIndex)) return;

                        const page = container.querySelector(`[data-page-index="${pageIndex}"]`);
                        if (!page) return;

                        const stickerImages = page.querySelectorAll('.sticker-glued img[data-src]');
                        stickerImages.forEach(img => {
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                        });

                        this.loadedPages.add(pageIndex);
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
                        this.loadStickersForVisiblePages(pageIndex);
                    }
                },

                destroy() {
                    if (this.pageFlip) {
                        this.pageFlip.destroy();
                        this.pageFlip = null;
                    }
                    if (this.stickerObserver) {
                        this.stickerObserver.disconnect();
                        this.stickerObserver = null;
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
            padding: 0;
            overflow: hidden;
        }

        /* Glued stickers styling */
        .sticker-glued {
            z-index: 5;
            pointer-events: none;
            transition: transform 0.2s ease;
        }

        .sticker-glued .sticker-image {
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
        }

        /* Sticker slots (empty and available) */
        .sticker-slot {
            z-index: 4;
            pointer-events: auto;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sticker-slot:hover {
            z-index: 15;
        }

        /* Empty sticker slot */
        .sticker-empty > div {
            transition: all 0.2s ease;
        }

        .sticker-empty:hover > div {
            border-color: rgba(156, 163, 175, 0.7);
            background-color: rgba(107, 114, 128, 0.15);
        }

        /* Available sticker slot (ready to be glued) */
        .sticker-available > div {
            transition: all 0.2s ease;
            animation: available-pulse 2s ease-in-out infinite;
        }

        .sticker-available:hover > div {
            border-color: rgb(16, 185, 129);
            background-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
        }

        @keyframes available-pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            50% {
                box-shadow: 0 0 8px 2px rgba(16, 185, 129, 0.3);
            }
        }

        /* Tooltip styling */
        .sticker-tooltip {
            font-size: 0.65rem;
            max-width: 120px;
            text-align: center;
            line-height: 1.2;
        }

        @media (min-width: 640px) {
            .sticker-tooltip {
                font-size: 0.75rem;
                max-width: 150px;
            }
        }

        /* Page flip library overrides */
        .stf__parent {
            margin: 0 !important;
            padding: 0 !important;
        }

        .stf__wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .stf__wrapper--landscape {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Dark mode adjustments */
        .dark .album-container .page {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
    </style>
</div>
