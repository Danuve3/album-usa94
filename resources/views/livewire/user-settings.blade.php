<div class="space-y-6">
    {{-- Success message --}}
    @if ($showSuccessMessage)
        <div
            class="rounded-lg bg-emerald-50 p-4 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400"
            x-data="{ show: true }"
            x-init="setTimeout(() => { show = false; $wire.showSuccessMessage = false }, 3000)"
            x-show="show"
            x-transition
        >
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Cambios guardados correctamente</span>
            </div>
        </div>
    @endif

    {{-- Avatar section --}}
    <div class="rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Foto de perfil</h2>

        <div class="flex flex-col items-center gap-6 sm:flex-row">
            {{-- Current avatar --}}
            <div class="relative">
                <img
                    src="{{ $user->avatar_url }}"
                    alt="{{ $user->name }}"
                    class="h-32 w-32 rounded-full object-cover ring-4 ring-gray-100 dark:ring-gray-700"
                >
                @if ($user->avatar)
                    <button
                        wire:click="removeAvatar"
                        wire:confirm="¿Estás seguro de que quieres eliminar tu foto de perfil?"
                        class="absolute -right-1 -top-1 rounded-full bg-red-500 p-1.5 text-white shadow-lg transition-transform hover:scale-110"
                        title="Eliminar foto"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Upload form --}}
            <div class="flex-1 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cambiar foto
                    </label>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        JPG, PNG o GIF. Máximo 2MB.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <label class="cursor-pointer">
                        <span class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Seleccionar imagen
                        </span>
                        <input
                            type="file"
                            wire:model="avatar"
                            accept="image/*"
                            class="hidden"
                        >
                    </label>

                    @if ($avatar)
                        <button
                            wire:click="saveAvatar"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-600 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="saveAvatar">Guardar foto</span>
                            <span wire:loading wire:target="saveAvatar">Guardando...</span>
                        </button>
                    @endif
                </div>

                {{-- Preview --}}
                @if ($avatar)
                    <div class="mt-4">
                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Vista previa:</p>
                        <img
                            src="{{ $avatar->temporaryUrl() }}"
                            alt="Vista previa"
                            class="h-24 w-24 rounded-full object-cover ring-2 ring-emerald-500"
                        >
                    </div>
                @endif

                @error('avatar')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Name section --}}
    <div class="rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Nombre de usuario</h2>

        <form wire:submit="saveName" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nombre
                </label>
                <input
                    type="text"
                    id="name"
                    wire:model="name"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-600 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="saveName">Guardar nombre</span>
                <span wire:loading wire:target="saveName">Guardando...</span>
            </button>
        </form>
    </div>

    {{-- Account info --}}
    <div class="rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Información de la cuenta</h2>

        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->email }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Miembro desde</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Cromos totales</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->total_stickers_count }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Cromos pegados</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->glued_stickers_count }}</dd>
            </div>
        </dl>
    </div>
</div>
