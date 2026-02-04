<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Suspendida - USA 94</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        {{-- Card --}}
        <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="bg-red-600 px-6 py-8 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Cuenta Suspendida</h1>
            </div>

            {{-- Content --}}
            <div class="px-6 py-8">
                <p class="text-gray-300 text-center mb-6">
                    Tu cuenta ha sido suspendida y no puedes acceder al álbum en este momento.
                </p>

                @if ($reason)
                    <div class="bg-gray-700/50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-400 mb-1">Motivo:</p>
                        <p class="text-white">{{ $reason }}</p>
                    </div>
                @endif

                @if ($bannedAt)
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Fecha de suspensión: {{ $bannedAt->format('d/m/Y H:i') }}
                    </p>
                @endif

                <p class="text-gray-400 text-sm text-center mb-6">
                    Si crees que esto es un error, contacta con el administrador.
                </p>

                {{-- Logout button --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                    >
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-gray-600 text-center text-sm mt-6">
            USA 94 - Álbum Virtual
        </p>
    </div>
</body>
</html>
