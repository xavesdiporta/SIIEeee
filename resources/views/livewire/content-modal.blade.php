<div x-data="{ show: @entangle('show') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-2xl">
        <button wire:click="close" class="absolute top-4 right-4 z-10 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="bg-white px-8 pt-10 pb-8">
            <div class="flex flex-col items-center justify-center pb-6">
                <h2 class="text-3xl font-bold tracking-tight text-center" style="color: {{ $color }}">
                    {{ $category }}
                </h2>
                <div class="w-12 h-1.5 mt-4 rounded-full opacity-80" style="background-color: {{ $color }}"></div>
            </div>

            <div class="mb-8">
                <p class="text-[#665039] text-xl leading-relaxed font-light text-center">
                    {{ $content }}
                </p>
            </div>

            <div class="w-full space-y-3">
                <label for="note" class="block text-sm font-semibold text-gray-500 uppercase tracking-wide pl-1">
                    {{ __('Minhas Reflexões / Notas') }}
                </label>
                <div class="relative">
                    <textarea id="note" wire:model="note" rows="5" class="w-full p-4 text-gray-700 bg-[#FAF7F5] border-0 rounded-2xl focus:ring-2 focus:bg-white transition-all duration-200 resize-none placeholder-gray-400 text-base" style="--tw-ring-color: {{ $color }}" placeholder="Escreva aqui as suas observações, metas ou reflexões sobre este ponto..."></textarea>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-8 py-6 flex justify-center gap-4">
            <button wire:click="close"
                    class="px-8 py-3 text-sm font-medium text-gray-500 transition-colors duration-200 bg-white border border-gray-200 rounded-full hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                {{ __('Cancelar') }}
            </button>

            <button
                @click="show = false; $wire.saveNote()"
                class="px-10 py-3 text-sm font-bold text-white transition-transform duration-200 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                style="background-color: {{ $color }}; --tw-ring-color: {{ $color }}">
                {{ __('Guardar Nota') }}
            </button>
        </div>
    </div>
</div>
