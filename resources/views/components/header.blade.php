<header class="bg-white shadow-sm dark:bg-gray-800">
    <div class="mx-auto max-w-4xl px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('album') }}" class="text-xl font-semibold text-gray-900 dark:text-white">
                USA 94
            </a>

            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
