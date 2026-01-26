<x-layouts.app>
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                    USA 94
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Inicia sesión para continuar tu colección
                </p>
            </div>

            <div class="rounded-2xl bg-white p-8 shadow-xl dark:bg-gray-800">
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Correo electrónico
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:focus:border-emerald-400"
                            placeholder="tu@email.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Contraseña
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:focus:border-emerald-400"
                            placeholder="Tu contraseña"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            Recordarme
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        Iniciar sesión
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">
                        Regístrate
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
