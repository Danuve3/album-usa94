@php
    $pendingTradesCount = 0;
    $unopenedPacksCount = 0;
    if (auth()->check()) {
        $pendingTradesCount = \App\Models\Trade::where('receiver_id', auth()->id())
            ->pending()
            ->active()
            ->count();
        $unopenedPacksCount = auth()->user()->unopened_packs_count;
    }
@endphp

<header class="sticky top-0 z-50 bg-white/95 shadow-sm backdrop-blur-sm dark:bg-gray-800/95">
    <div class="mx-auto max-w-7xl px-4 py-3">
        <div class="flex items-center justify-between">
            {{-- Logo --}}
            <a href="{{ route('album') }}" class="flex items-center gap-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-sm font-black text-white">
                    94
                </span>
                <span>USA 94</span>
            </a>

            {{-- Navigation --}}
            <nav class="hidden items-center gap-1 sm:flex">
                <a
                    href="{{ route('album') }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('album') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                >
                    Album
                </a>
                <a
                    href="{{ route('trades') }}"
                    class="relative rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('trades') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                >
                    Intercambios
                    @if ($pendingTradesCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white">
                            {{ $pendingTradesCount > 99 ? '99+' : $pendingTradesCount }}
                        </span>
                    @endif
                </a>
                <a
                    href="{{ route('market') }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('market') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                >
                    Mercado
                </a>
                <a
                    href="{{ route('stats') }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('stats') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                >
                    Stats
                </a>
            </nav>

            {{-- User section --}}
            <div class="flex items-center gap-3">
                {{-- Notifications --}}
                <livewire:notification-bell />

                {{-- Packs counter --}}
                <a href="{{ route('album') }}#pack-pile" class="flex cursor-pointer items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 transition-colors hover:bg-amber-100 dark:bg-amber-900/30 dark:hover:bg-amber-900/50">
                    <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                        {{ $unopenedPacksCount }}
                    </span>
                    <span class="hidden text-xs text-amber-600 sm:inline dark:text-amber-400">
                        {{ $unopenedPacksCount === 1 ? 'sobre' : 'sobres' }}
                    </span>
                </a>

                {{-- User name --}}
                <span class="hidden text-sm font-medium text-gray-700 md:inline dark:text-gray-300">
                    {{ auth()->user()->name }}
                </span>

                {{-- Logout button --}}
                <form method="POST" action="{{ route('logout') }}" x-data x-on:submit="$el.querySelector('input[name=_token]').value = document.querySelector('meta[name=csrf-token]').content">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Salir</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <nav class="mt-3 flex items-center justify-center gap-1 border-t border-gray-100 pt-3 sm:hidden dark:border-gray-700">
            <a
                href="{{ route('album') }}"
                class="flex-1 rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('album') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Album
            </a>
            <a
                href="{{ route('trades') }}"
                class="relative flex-1 rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('trades') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Intercambios
                @if ($pendingTradesCount > 0)
                    <span class="absolute right-2 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white">
                        {{ $pendingTradesCount > 9 ? '9+' : $pendingTradesCount }}
                    </span>
                @endif
            </a>
            <a
                href="{{ route('market') }}"
                class="flex-1 rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('market') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Mercado
            </a>
            <a
                href="{{ route('stats') }}"
                class="flex-1 rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('stats') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Stats
            </a>
        </nav>
    </div>
</header>
