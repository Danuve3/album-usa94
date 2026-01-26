<x-layouts.app>
    <x-header />

    <main class="px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <h1 class="mb-8 text-2xl font-semibold text-gray-900 dark:text-white">
                Mi Álbum USA 94
            </h1>

            <div class="rounded-2xl bg-white p-8 shadow-xl dark:bg-gray-800">
                <p class="text-center text-gray-600 dark:text-gray-400">
                    ¡Bienvenido, {{ auth()->user()->name }}!
                </p>
                <p class="mt-4 text-center text-gray-500 dark:text-gray-500">
                    Tu colección de cromos del Mundial USA 94 está lista para comenzar.
                </p>
            </div>
        </div>
    </main>
</x-layouts.app>
