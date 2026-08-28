<x-app-layout>
    <div class="max-w-2xl mx-auto py-8 px-6">

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-[#776246] hover:text-[#3E2D1B] mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar à dashboard
        </a>

        <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-8">
            <h2 class="text-xl font-bold text-[#3E2D1B] mb-1">Submeter Nota de Progresso</h2>
            <p class="text-sm text-[#776246] mb-6">Conta o que fizeste para este objetivo. O teu Chefe vai rever e aprovar.</p>

            @if (session('status'))
                <div class="mb-6 bg-[#EAF3DE] border border-[#B7D7A0] text-[#173404] text-sm rounded-xl px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('progress-notes.store') }}" class="flex flex-col gap-5">
                @csrf

                <div>
                    <label for="reference" class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Referência</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference', $reference) }}"
                           class="w-full rounded-xl border-[#E4D5C3] bg-[#FAF7F5] text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]"
                           placeholder="ex: F3" required>
                    @error('reference')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">O que fizeste</label>
                    <textarea name="description" id="description" rows="6"
                              class="w-full rounded-xl border-[#E4D5C3] bg-[#FAF7F5] text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]"
                              placeholder="Descreve a atividade, o que aprendeste, quando aconteceu...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="self-start bg-[#3E2D1B] hover:bg-[#2A1F13] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                    Submeter para revisão
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
