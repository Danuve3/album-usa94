<div class="space-y-6">
    {{-- Album Completion Card --}}
    <div class="rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
            <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Progreso del Álbum
        </h2>
        <div class="flex flex-col items-center justify-center gap-4 sm:flex-row sm:gap-8">
            {{-- Circular Progress --}}
            <div class="relative h-32 w-32">
                <svg class="h-32 w-32 -rotate-90 transform" viewBox="0 0 120 120">
                    <circle
                        cx="60"
                        cy="60"
                        r="54"
                        stroke="currentColor"
                        stroke-width="12"
                        fill="none"
                        class="text-gray-200 dark:text-gray-700"
                    />
                    <circle
                        cx="60"
                        cy="60"
                        r="54"
                        stroke="currentColor"
                        stroke-width="12"
                        fill="none"
                        stroke-linecap="round"
                        class="text-emerald-500 transition-all duration-500"
                        stroke-dasharray="{{ 339.292 }}"
                        stroke-dashoffset="{{ 339.292 - (339.292 * $completionPercentage / 100) }}"
                    />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $completionPercentage }}%</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">completado</span>
                </div>
            </div>
            {{-- Stats Numbers --}}
            <div class="flex flex-col gap-2 text-center sm:text-left">
                <div>
                    <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $gluedStickers }}</span>
                    <span class="text-gray-500 dark:text-gray-400"> / {{ $totalStickers }}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">cromos pegados en el álbum</p>
                <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Faltan <span class="font-bold text-amber-600 dark:text-amber-400">{{ $totalStickers - $gluedStickers }}</span> cromos
                </p>
            </div>
        </div>
    </div>

    {{-- Shiny Stats --}}
    <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-yellow-50 p-6 shadow-lg dark:from-amber-900/20 dark:to-yellow-900/20">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
            <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
            </svg>
            Cromos Shiny
        </h2>
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $ownedShiny }}</span>
                    <span class="text-gray-500 dark:text-gray-400">/ {{ $totalShiny }}</span>
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">cromos shiny obtenidos</p>
            </div>
            <div class="h-2 flex-1 overflow-hidden rounded-full bg-amber-200 dark:bg-amber-900/40">
                <div
                    class="h-full rounded-full bg-gradient-to-r from-amber-400 to-yellow-300 transition-all duration-500"
                    style="width: {{ $totalShiny > 0 ? round(($ownedShiny / $totalShiny) * 100) : 0 }}%"
                ></div>
            </div>
            <span class="text-sm font-medium text-amber-700 dark:text-amber-300">
                {{ $totalShiny > 0 ? round(($ownedShiny / $totalShiny) * 100) : 0 }}%
            </span>
        </div>
    </div>

    {{-- Packs History --}}
    <div class="rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
            <svg class="h-5 w-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Historial de Sobres
        </h2>
        <div class="mb-4 flex items-center gap-4">
            <div class="rounded-xl bg-purple-50 px-4 py-3 dark:bg-purple-900/20">
                <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $totalPacksOpened }}</span>
                <p class="text-xs text-purple-600/70 dark:text-purple-400/70">sobres abiertos</p>
            </div>
        </div>
        @if (count($packsHistory) > 0)
            <div class="overflow-x-auto">
                <div class="flex h-32 items-end gap-1" style="min-width: {{ count($packsHistory) * 24 }}px">
                    @php
                        $maxPacks = max(array_column($packsHistory, 'packs'));
                    @endphp
                    @foreach ($packsHistory as $item)
                        <div class="group relative flex flex-1 flex-col items-center" style="min-width: 20px">
                            <div
                                class="w-full rounded-t bg-purple-500 transition-all hover:bg-purple-600 dark:bg-purple-400 dark:hover:bg-purple-300"
                                style="height: {{ $maxPacks > 0 ? ($item['packs'] / $maxPacks) * 100 : 0 }}%"
                            ></div>
                            <div class="absolute -top-8 hidden rounded bg-gray-900 px-2 py-1 text-xs text-white group-hover:block dark:bg-gray-700">
                                {{ $item['packs'] }} sobres
                                <br>
                                <span class="text-gray-400">{{ \Carbon\Carbon::parse($item['date'])->format('d/m') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">Últimos 30 días con actividad</p>
        @else
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">No hay sobres abiertos aún</p>
        @endif
    </div>

    {{-- Progress Over Time --}}
    <div class="rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            Progreso en el Tiempo
        </h2>
        @if (count($progressHistory) > 0)
            <div class="overflow-x-auto">
                <div class="relative h-40" style="min-width: {{ max(count($progressHistory) * 40, 200) }}px">
                    @php
                        $maxStickers = max(array_column($progressHistory, 'stickers'));
                        $points = [];
                        $count = count($progressHistory);
                        foreach ($progressHistory as $index => $item) {
                            $x = $count > 1 ? ($index / ($count - 1)) * 100 : 50;
                            $y = $maxStickers > 0 ? 100 - (($item['stickers'] / $maxStickers) * 100) : 100;
                            $points[] = "{$x}%,{$y}%";
                        }
                    @endphp
                    <svg class="h-full w-full" preserveAspectRatio="none">
                        {{-- Grid lines --}}
                        @for ($i = 0; $i <= 4; $i++)
                            <line
                                x1="0"
                                y1="{{ $i * 25 }}%"
                                x2="100%"
                                y2="{{ $i * 25 }}%"
                                stroke="currentColor"
                                stroke-width="1"
                                class="text-gray-100 dark:text-gray-700"
                            />
                        @endfor
                        {{-- Area fill --}}
                        <polygon
                            points="0%,100% {{ implode(' ', $points) }} 100%,100%"
                            class="fill-blue-100 dark:fill-blue-900/30"
                        />
                        {{-- Line --}}
                        <polyline
                            points="{{ implode(' ', $points) }}"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="text-blue-500"
                        />
                        {{-- Points --}}
                        @foreach ($progressHistory as $index => $item)
                            @php
                                $x = $count > 1 ? ($index / ($count - 1)) * 100 : 50;
                                $y = $maxStickers > 0 ? 100 - (($item['stickers'] / $maxStickers) * 100) : 100;
                            @endphp
                            <circle
                                cx="{{ $x }}%"
                                cy="{{ $y }}%"
                                r="4"
                                class="fill-blue-500"
                            />
                        @endforeach
                    </svg>
                </div>
                <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    @if (count($progressHistory) > 0)
                        <span>{{ \Carbon\Carbon::parse($progressHistory[0]['date'])->format('d/m/Y') }}</span>
                        <span>{{ \Carbon\Carbon::parse($progressHistory[count($progressHistory) - 1]['date'])->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
            <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">Cromos únicos obtenidos (acumulativo)</p>
        @else
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">No hay datos de progreso aún</p>
        @endif
    </div>

    {{-- Stats by Page --}}
    <div class="rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
            <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
            </svg>
            Progreso por Página
        </h2>
        @if (count($statsByPage) > 0)
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($statsByPage as $page)
                    <div class="rounded-xl border border-gray-100 p-3 transition-colors hover:border-indigo-200 hover:bg-indigo-50/50 dark:border-gray-700 dark:hover:border-indigo-800 dark:hover:bg-indigo-900/20">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $page['name'] }}</span>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $page['percentage'] === 100 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $page['percentage'] }}%
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                <div
                                    class="h-full rounded-full transition-all duration-300 {{ $page['percentage'] === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                                    style="width: {{ $page['percentage'] }}%"
                                ></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $page['glued'] }}/{{ $page['total'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">No hay páginas disponibles</p>
        @endif
    </div>
</div>
