@php
    $pendingTradesCount = 0;
    if (auth()->check()) {
        $pendingTradesCount = \App\Models\Trade::where('receiver_id', auth()->id())
            ->pending()
            ->active()
            ->count();
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
                    Álbum
                </a>
                <a
                    href="{{ route('my-stickers') }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('my-stickers') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                >
                    Mis Cromos
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
                    Estadísticas
                </a>
                <a
                    href="{{ route('get-packs') }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('get-packs') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                >
                    Obtener sobres
                </a>
            </nav>

            {{-- User section --}}
            <div class="flex items-center gap-3">
                {{-- Notifications --}}
                <livewire:notification-bell />

                {{-- Packs counter with countdown --}}
                <livewire:pack-counter />

                {{-- User dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button
                        x-on:click="open = !open"
                        class="flex items-center gap-2 rounded-lg px-2 py-1 transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <img
                            src="{{ auth()->user()->avatar_url }}"
                            alt="{{ auth()->user()->name }}"
                            class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                        >
                        <span class="hidden text-sm font-medium text-gray-700 md:inline dark:text-gray-300">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown menu --}}
                    <div
                        x-show="open"
                        x-on:click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-lg bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-700"
                        x-cloak
                    >
                        <a
                            href="{{ route('settings') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Ajustes
                        </a>
                        <hr class="my-1 border-gray-200 dark:border-gray-600">
                        <form method="POST" action="{{ route('logout') }}" x-data x-on:submit="$el.querySelector('input[name=_token]').value = document.querySelector('meta[name=csrf-token]').content">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <nav class="mt-3 flex flex-wrap items-center justify-center gap-1 border-t border-gray-100 pt-3 sm:hidden dark:border-gray-700">
            <a
                href="{{ route('album') }}"
                class="rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('album') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Álbum
            </a>
            <a
                href="{{ route('my-stickers') }}"
                class="rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('my-stickers') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Cromos
            </a>
            <a
                href="{{ route('trades') }}"
                class="relative rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('trades') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Intercambios
                @if ($pendingTradesCount > 0)
                    <span class="absolute right-0 top-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white">
                        {{ $pendingTradesCount > 9 ? '9+' : $pendingTradesCount }}
                    </span>
                @endif
            </a>
            <a
                href="{{ route('market') }}"
                class="rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('market') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Mercado
            </a>
            <a
                href="{{ route('stats') }}"
                class="rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('stats') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Stats
            </a>
            <a
                href="{{ route('get-packs') }}"
                class="rounded-lg px-3 py-2 text-center text-sm font-medium transition-colors {{ request()->routeIs('get-packs') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
            >
                Sobres
            </a>
        </nav>
    </div>
</header>
