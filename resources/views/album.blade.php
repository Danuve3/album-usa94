<x-layouts.app>
    <div class="min-h-screen px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Mi Álbum USA 94
                </h1>
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

            <div class="rounded-2xl bg-white p-8 shadow-xl dark:bg-gray-800">
                <p class="text-center text-gray-600 dark:text-gray-400">
                    ¡Bienvenido, {{ auth()->user()->name }}!
                </p>
                <p class="mt-4 text-center text-gray-500 dark:text-gray-500">
                    Tu colección de cromos del Mundial USA 94 está lista para comenzar.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
