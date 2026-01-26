<div class="p-6 max-w-sm mx-auto bg-white rounded-xl shadow-lg flex items-center space-x-4">
    <div>
        <div class="text-xl font-medium text-black">Hello World!</div>
        <p class="text-slate-500">Livewire is working correctly.</p>
        <p class="text-slate-500 mt-2">Count: {{ $count }}</p>
        <button wire:click="increment" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Increment
        </button>
    </div>
</div>
