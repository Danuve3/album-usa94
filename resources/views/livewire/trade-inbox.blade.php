<div class="flex flex-col gap-6">
    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="rounded-lg bg-emerald-100 p-4 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="rounded-2xl bg-white shadow-xl dark:bg-gray-800">
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button
                type="button"
                wire:click="setTab('received')"
                class="relative flex-1 px-4 py-4 text-center font-medium transition {{ $activeTab === 'received' ? 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
            >
                <span class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <span class="hidden sm:inline">Recibidos</span>
                    @if ($pendingReceivedCount > 0)
                        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-xs font-bold text-white">
                            {{ $pendingReceivedCount }}
                        </span>
                    @endif
                </span>
            </button>
            <button
                type="button"
                wire:click="setTab('sent')"
                class="relative flex-1 px-4 py-4 text-center font-medium transition {{ $activeTab === 'sent' ? 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
            >
                <span class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span class="hidden sm:inline">Enviados</span>
                    @if ($pendingSentCount > 0)
                        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-xs font-bold text-white">
                            {{ $pendingSentCount }}
                        </span>
                    @endif
                </span>
            </button>
            <button
                type="button"
                wire:click="setTab('completed')"
                class="relative flex-1 px-4 py-4 text-center font-medium transition {{ $activeTab === 'completed' ? 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
            >
                <span class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">Historial</span>
                </span>
            </button>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- Received Tab --}}
            @if ($activeTab === 'received')
                @if ($receivedTrades->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">No hay propuestas pendientes</h3>
                        <p class="text-gray-500 dark:text-gray-400">Cuando otros usuarios te envíen propuestas de intercambio aparecerán aquí.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($receivedTrades as $trade)
                            <button
                                type="button"
                                wire:click="selectTrade({{ $trade->id }})"
                                class="flex w-full items-center justify-between rounded-xl border p-4 text-left transition hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $selectedTradeId === $trade->id ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700' }}"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <span class="text-sm font-medium">{{ strtoupper(substr($trade->sender->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $trade->sender->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $trade->offeredItems->count() }} cromos por {{ $trade->requestedItems->count() }} tuyos
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $trade->created_at->diffForHumans() }}
                                    </span>
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif

            {{-- Sent Tab --}}
            @elseif ($activeTab === 'sent')
                @if ($sentTrades->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">No has enviado propuestas</h3>
                        <p class="text-gray-500 dark:text-gray-400">Busca usuarios arriba para proponer intercambios de cromos.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($sentTrades as $trade)
                            <button
                                type="button"
                                wire:click="selectTrade({{ $trade->id }})"
                                class="flex w-full items-center justify-between rounded-xl border p-4 text-left transition hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $selectedTradeId === $trade->id ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700' }}"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <span class="text-sm font-medium">{{ strtoupper(substr($trade->receiver->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $trade->receiver->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Ofreces {{ $trade->offeredItems->count() }} por {{ $trade->requestedItems->count() }} cromos
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $trade->created_at->diffForHumans() }}
                                    </span>
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif

            {{-- Completed/History Tab --}}
            @else
                @if ($completedTrades->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">Sin historial de intercambios</h3>
                        <p class="text-gray-500 dark:text-gray-400">Aquí aparecerán los intercambios completados, rechazados o cancelados.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($completedTrades as $trade)
                            @php
                                $isReceived = $trade->receiver_id === auth()->id();
                                $otherUser = $isReceived ? $trade->sender : $trade->receiver;
                                $isExpired = $trade->status->value === 'pending' && $trade->isExpired();
                                $statusConfig = [
                                    'accepted' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Aceptado'],
                                    'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Rechazado'],
                                    'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400', 'label' => 'Cancelado'],
                                    'expired' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400', 'label' => 'Expirado'],
                                ];
                                $status = $isExpired ? 'expired' : $trade->status->value;
                                $config = $statusConfig[$status] ?? $statusConfig['cancelled'];
                            @endphp
                            <button
                                type="button"
                                wire:click="selectTrade({{ $trade->id }})"
                                class="flex w-full items-center gap-3 rounded-xl border p-3 text-left transition hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $selectedTradeId === $trade->id ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700' }}"
                            >
                                {{-- Status Badge --}}
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                </div>

                                {{-- Direction & User --}}
                                <div class="flex min-w-0 flex-1 items-center gap-2">
                                    <span class="flex-shrink-0 rounded px-1.5 py-0.5 text-xs font-medium {{ $isReceived ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                        {{ $isReceived ? 'Recibido' : 'Enviado' }}
                                    </span>
                                    <span class="truncate text-sm text-gray-600 dark:text-gray-300">
                                        {{ $isReceived ? 'de' : 'a' }} <span class="font-medium text-gray-900 dark:text-white">{{ $otherUser->name }}</span>
                                    </span>
                                </div>

                                {{-- Sticker counts --}}
                                <div class="hidden flex-shrink-0 items-center gap-1 text-xs text-gray-500 dark:text-gray-400 sm:flex">
                                    <span class="rounded bg-emerald-50 px-1.5 py-0.5 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                                        {{ $trade->offeredItems->count() }}
                                    </span>
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    <span class="rounded bg-amber-50 px-1.5 py-0.5 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                                        {{ $trade->requestedItems->count() }}
                                    </span>
                                </div>

                                {{-- Dates --}}
                                <div class="hidden flex-shrink-0 flex-col items-end text-right text-[10px] leading-tight text-gray-400 dark:text-gray-500 lg:flex">
                                    <span>{{ $trade->created_at->format('d/m/Y') }}</span>
                                    @if ($trade->updated_at && $trade->updated_at->ne($trade->created_at))
                                        <span class="text-gray-500 dark:text-gray-400">{{ $trade->updated_at->format('d/m/Y') }}</span>
                                    @endif
                                </div>

                                {{-- Arrow --}}
                                <svg class="h-4 w-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Trade Detail Modal --}}
    @if ($selectedTrade)
        @php
            $isReceivedTrade = $selectedTrade->receiver_id === auth()->id();
            $otherUserInTrade = $isReceivedTrade ? $selectedTrade->sender : $selectedTrade->receiver;
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="closeDetail">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800" wire:click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <span class="text-sm font-medium">{{ strtoupper(substr($otherUserInTrade->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                @if ($isReceivedTrade)
                                    Propuesta de {{ $otherUserInTrade->name }}
                                @else
                                    Propuesta a {{ $otherUserInTrade->name }}
                                @endif
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $selectedTrade->created_at->format('d/m/Y H:i') }}
                                @if ($selectedTrade->isPending() && !$selectedTrade->isExpired() && $selectedTrade->expires_at)
                                    · Expira {{ $selectedTrade->expires_at->diffForHumans() }}
                                @elseif (!$selectedTrade->isPending() && $selectedTrade->updated_at)
                                    · Finalizado {{ $selectedTrade->updated_at->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="max-h-[60vh] overflow-y-auto p-6">
                    <div class="grid gap-6 sm:grid-cols-2">
                        {{-- Offered Items --}}
                        <div>
                            <h4 class="mb-3 flex items-center gap-2 font-medium text-emerald-700 dark:text-emerald-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                                @if ($isReceivedTrade)
                                    Te ofrecen ({{ $selectedTrade->offeredItems->count() }})
                                @else
                                    Ofreces ({{ $selectedTrade->offeredItems->count() }})
                                @endif
                            </h4>
                            <div class="rounded-lg border-2 border-dashed border-emerald-300 bg-emerald-50/50 p-3 dark:border-emerald-700 dark:bg-emerald-900/10">
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach ($selectedTrade->offeredItems as $item)
                                        @if ($item->userSticker && $item->userSticker->sticker)
                                            <div class="group relative aspect-[3/4] rounded-lg shadow-md {{ $item->userSticker->sticker->rarity->value === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}">
                                                <div class="flex h-full flex-col items-center justify-center p-1">
                                                    @if ($item->userSticker->sticker->image_path)
                                                        <img
                                                            src="{{ Storage::url($item->userSticker->sticker->image_path) }}"
                                                            alt="{{ $item->userSticker->sticker->name }}"
                                                            class="h-full w-full rounded object-contain"
                                                            loading="lazy"
                                                        />
                                                    @else
                                                        <span class="text-lg font-bold {{ $item->userSticker->sticker->rarity->value === 'shiny' ? 'text-amber-800' : 'text-gray-800 dark:text-white' }}">
                                                            {{ $item->userSticker->sticker->number }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                                    #{{ $item->userSticker->sticker->number }}
                                                </div>
                                                <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                                    {{ $item->userSticker->sticker->name }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Requested Items --}}
                        <div>
                            <h4 class="mb-3 flex items-center gap-2 font-medium text-amber-700 dark:text-amber-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                </svg>
                                @if ($isReceivedTrade)
                                    Te piden ({{ $selectedTrade->requestedItems->count() }})
                                @else
                                    Solicitas ({{ $selectedTrade->requestedItems->count() }})
                                @endif
                            </h4>
                            <div class="rounded-lg border-2 border-dashed border-amber-300 bg-amber-50/50 p-3 dark:border-amber-700 dark:bg-amber-900/10">
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach ($selectedTrade->requestedItems as $item)
                                        @if ($item->userSticker && $item->userSticker->sticker)
                                            <div class="group relative aspect-[3/4] rounded-lg shadow-md {{ $item->userSticker->sticker->rarity->value === 'shiny' ? 'sticker-shiny' : 'bg-white dark:bg-gray-700' }}">
                                                <div class="flex h-full flex-col items-center justify-center p-1">
                                                    @if ($item->userSticker->sticker->image_path)
                                                        <img
                                                            src="{{ Storage::url($item->userSticker->sticker->image_path) }}"
                                                            alt="{{ $item->userSticker->sticker->name }}"
                                                            class="h-full w-full rounded object-contain"
                                                            loading="lazy"
                                                        />
                                                    @else
                                                        <span class="text-lg font-bold {{ $item->userSticker->sticker->rarity->value === 'shiny' ? 'text-amber-800' : 'text-gray-800 dark:text-white' }}">
                                                            {{ $item->userSticker->sticker->number }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                                    #{{ $item->userSticker->sticker->number }}
                                                </div>
                                                <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                                    {{ $item->userSticker->sticker->name }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900/50">
                    @if ($selectedTrade->isPending() && !$selectedTrade->isExpired())
                        @if ($isReceivedTrade)
                            <div class="flex gap-3">
                                <button
                                    type="button"
                                    wire:click="openConfirmModal('reject')"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50"
                                    wire:target="openConfirmModal"
                                    class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <span wire:loading.remove wire:target="openConfirmModal">Rechazar</span>
                                    <span wire:loading wire:target="openConfirmModal">...</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="openConfirmModal('accept')"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50"
                                    wire:target="openConfirmModal"
                                    class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 font-medium text-white transition hover:bg-emerald-700"
                                >
                                    <span wire:loading.remove wire:target="openConfirmModal">Aceptar Intercambio</span>
                                    <span wire:loading wire:target="openConfirmModal">...</span>
                                </button>
                            </div>
                        @else
                            <button
                                type="button"
                                wire:click="openConfirmModal('cancel')"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                wire:target="openConfirmModal"
                                class="w-full rounded-lg border border-red-300 bg-white px-4 py-2.5 font-medium text-red-600 transition hover:bg-red-50 dark:border-red-600 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20"
                            >
                                <span wire:loading.remove wire:target="openConfirmModal">Cancelar Propuesta</span>
                                <span wire:loading wire:target="openConfirmModal">...</span>
                            </button>
                        @endif
                    @else
                        <div class="text-center">
                            @php
                                $statusMessages = [
                                    'accepted' => 'Este intercambio fue completado exitosamente.',
                                    'rejected' => 'Este intercambio fue rechazado.',
                                    'cancelled' => 'Este intercambio fue cancelado.',
                                ];
                            @endphp
                            @if ($selectedTrade->isExpired() && $selectedTrade->isPending())
                                <p class="text-gray-500 dark:text-gray-400">Esta propuesta ha expirado.</p>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">{{ $statusMessages[$selectedTrade->status->value] ?? '' }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm Modal --}}
    @if ($showConfirmModal)
        <div
            wire:key="confirm-modal-{{ $confirmAction }}"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
            wire:click.self="cancelConfirm"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800" wire:click.stop>
                @php
                    $confirmData = [
                        'accept' => [
                            'icon' => 'text-emerald-600 dark:text-emerald-400',
                            'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                            'title' => 'Aceptar Intercambio',
                            'message' => 'Se transferirán los cromos entre ambas cuentas. Esta acción no se puede deshacer.',
                            'button' => 'Aceptar',
                            'buttonClass' => 'bg-emerald-600 hover:bg-emerald-700',
                        ],
                        'reject' => [
                            'icon' => 'text-red-600 dark:text-red-400',
                            'iconBg' => 'bg-red-100 dark:bg-red-900/30',
                            'title' => 'Rechazar Propuesta',
                            'message' => 'El usuario será notificado que has rechazado su propuesta de intercambio.',
                            'button' => 'Rechazar',
                            'buttonClass' => 'bg-red-600 hover:bg-red-700',
                        ],
                        'cancel' => [
                            'icon' => 'text-gray-600 dark:text-gray-400',
                            'iconBg' => 'bg-gray-100 dark:bg-gray-700',
                            'title' => 'Cancelar Propuesta',
                            'message' => 'Tu propuesta será cancelada y el otro usuario ya no podrá aceptarla.',
                            'button' => 'Cancelar Propuesta',
                            'buttonClass' => 'bg-gray-600 hover:bg-gray-700',
                        ],
                    ];
                    $data = $confirmData[$confirmAction] ?? $confirmData['cancel'];
                @endphp

                <div class="mb-4 flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full {{ $data['iconBg'] }}">
                        @if ($confirmAction === 'accept')
                            <svg class="h-8 w-8 {{ $data['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif ($confirmAction === 'reject')
                            <svg class="h-8 w-8 {{ $data['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @else
                            <svg class="h-8 w-8 {{ $data['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        @endif
                    </div>
                </div>
                <h3 class="mb-2 text-center text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $data['title'] }}
                </h3>
                <p class="mb-4 text-center text-gray-600 dark:text-gray-400">
                    {{ $data['message'] }}
                </p>

                {{-- Trade Details Summary --}}
                @if ($selectedTrade)
                    @php
                        $isReceivedInConfirm = $selectedTrade->receiver_id === auth()->id();
                    @endphp
                    <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Offered stickers --}}
                            <div>
                                <p class="mb-2 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                    @if ($isReceivedInConfirm)
                                        Te ofrecen
                                    @else
                                        Ofreces
                                    @endif
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($selectedTrade->offeredItems->take(6) as $item)
                                        @if ($item->userSticker && $item->userSticker->sticker)
                                            <span class="inline-flex items-center rounded bg-emerald-100 px-1.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                #{{ $item->userSticker->sticker->number }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if ($selectedTrade->offeredItems->count() > 6)
                                        <span class="inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            +{{ $selectedTrade->offeredItems->count() - 6 }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Requested stickers --}}
                            <div>
                                <p class="mb-2 text-xs font-medium text-amber-600 dark:text-amber-400">
                                    @if ($isReceivedInConfirm)
                                        Te piden
                                    @else
                                        Solicitas
                                    @endif
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($selectedTrade->requestedItems->take(6) as $item)
                                        @if ($item->userSticker && $item->userSticker->sticker)
                                            <span class="inline-flex items-center rounded bg-amber-100 px-1.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                                #{{ $item->userSticker->sticker->number }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if ($selectedTrade->requestedItems->count() > 6)
                                        <span class="inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            +{{ $selectedTrade->requestedItems->count() - 6 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="flex gap-3">
                    <button
                        type="button"
                        wire:click="cancelConfirm"
                        class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Volver
                    </button>
                    <button
                        type="button"
                        wire:click="executeAction"
                        wire:loading.attr="disabled"
                        class="flex-1 rounded-lg px-4 py-2.5 font-medium text-white transition disabled:opacity-50 {{ $data['buttonClass'] }}"
                    >
                        <span wire:loading.remove wire:target="executeAction">{{ $data['button'] }}</span>
                        <span wire:loading wire:target="executeAction">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .sticker-shiny {
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 50%, #fef3c7 100%);
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
    </style>
</div>
