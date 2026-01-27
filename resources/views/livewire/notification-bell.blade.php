<div
    class="relative"
    wire:poll.10s
    x-data="{ open: $wire.entangle('showDropdown') }"
    @click.away="open = false; $wire.closeDropdown()"
>
    {{-- Bell Button --}}
    <button
        type="button"
        wire:click="toggleDropdown"
        class="relative flex items-center justify-center rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if ($showDropdown)
        <div class="absolute right-0 top-full z-50 mt-2 w-80 rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 sm:w-96">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Notificaciones</h3>
                @if ($unreadCount > 0)
                    <button
                        type="button"
                        wire:click="markAllAsRead"
                        class="text-sm text-emerald-600 transition hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300"
                    >
                        Marcar todo como leído
                    </button>
                @endif
            </div>

            {{-- Notifications List --}}
            <div class="max-h-96 overflow-y-auto">
                @if ($notifications->isEmpty())
                    <div class="flex flex-col items-center justify-center px-4 py-8">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No tienes notificaciones</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $type = $data['type'] ?? 'unknown';
                                $iconConfig = match($type) {
                                    'trade_proposal' => [
                                        'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                                        'text' => 'text-blue-600 dark:text-blue-400',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />',
                                    ],
                                    'trade_accepted' => [
                                        'bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                                        'text' => 'text-emerald-600 dark:text-emerald-400',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                    ],
                                    'trade_rejected' => [
                                        'bg' => 'bg-red-100 dark:bg-red-900/30',
                                        'text' => 'text-red-600 dark:text-red-400',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                    ],
                                    default => [
                                        'bg' => 'bg-gray-100 dark:bg-gray-700',
                                        'text' => 'text-gray-600 dark:text-gray-400',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
                                    ],
                                };
                            @endphp
                            <a
                                href="{{ route('trades') }}"
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="flex gap-3 px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ is_null($notification->read_at) ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}"
                            >
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ $iconConfig['bg'] }}">
                                    <svg class="h-5 w-5 {{ $iconConfig['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        {!! $iconConfig['icon'] !!}
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-900 dark:text-white {{ is_null($notification->read_at) ? 'font-medium' : '' }}">
                                        {{ $data['message'] ?? 'Nueva notificación' }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @if (is_null($notification->read_at))
                                    <div class="flex-shrink-0">
                                        <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            @if ($notifications->isNotEmpty())
                <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                    <a
                        href="{{ route('trades') }}"
                        class="block text-center text-sm font-medium text-emerald-600 transition hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300"
                    >
                        Ver todos los intercambios
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
