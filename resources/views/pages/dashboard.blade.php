<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 h-[calc(100vh-8rem)]">

        @php
            $categories = [
                ['label' => 'F', 'name' => 'Físico', 'color' => '#16a34a', 'refs' => ['F1', 'F2', 'F3', 'F4', 'F5', 'F6']],
                ['label' => 'A', 'name' => 'Afectivo', 'color' => '#dc2626', 'refs' => ['A1', 'A2', 'A3', 'A4', 'A5', 'A6']],
                ['label' => 'C', 'name' => 'Carácter', 'color' => '#2563eb', 'refs' => ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8']],
                ['label' => 'E', 'name' => 'Espiritual', 'color' => '#9333ea', 'refs' => ['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8']],
                ['label' => 'I', 'name' => 'Intelectual', 'color' => '#f97316', 'refs' => ['I1', 'I2', 'I3', 'I4', 'I5', 'I6', 'I7']],
                ['label' => 'S', 'name' => 'Social', 'color' => '#eab308', 'refs' => ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7']],
            ];

            $completedRefs = \App\Models\ProgressNote::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->pluck('reference')
                ->toArray();
        @endphp

        {{-- LINHA DE TOPO: Círculo + Dados do utilizador --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-8mb-6 flex flex-col md:flex-row items-center gap-8">

            {{-- Círculo de Progresso --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="relative w-64 h-64 rounded-full shadow-lg"
                     style="background: conic-gradient(from 302deg,
                        #7F1D1D 0deg 116deg,
                        transparent 116deg 120deg,
                        #DC2626 120deg 236deg,
                        transparent 236deg 240deg,
                        #FECACA 240deg 356deg,
                        transparent 356deg 360deg
                     );">
                    <div class="absolute inset-8 bg-white rounded-full flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full bg-[#FAF7F5] border-4 border-[#F2ECE7] flex flex-col items-center justify-center shadow-inner overflow-hidden">
                            <img src="{{ asset('images/caminho.png') }}" alt="Caminho" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                {{-- Legenda --}}
                <div class="flex gap-4 mt-4">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#7F1D1D]"></div>
                        <span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Comunidade</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#DC2626]"></div>
                        <span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Partida</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#FECACA]"></div>
                        <span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Serviço</span>
                    </div>
                </div>
            </div>

            {{-- Divisor vertical --}}
            <div class="hidden md:block w-px self-stretch bg-[#E4D5C3]"></div>

            {{-- Dados do utilizador --}}
            <div class="flex-1 w-full">
                <h2 class="text-xl font-bold text-[#3E2D1B] mb-4">O meu Sistema de Progresso</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div>
                            <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Colaborador</p>
                            <p class="text-base text-[#3E2D1B] font-medium mt-0.5">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div>
                            <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Cargo</p>
                            <p class="text-base text-[#3E2D1B] font-medium mt-0.5">{{ Auth::user()->cargo ?? 'Não definido' }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- LINHA DE BAIXO: Grid de 2 colunas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUNA ESQUERDA (2/3): Tabela de progresso por dimensão --}}
            <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Detalhe por Dimensão</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    @foreach($categories as $cat)
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center justify-center w-7 h-7 rounded-md text-xs font-bold text-white shadow-sm"
                                     style="background-color: {{ $cat['color'] }};">
                                    {{ $cat['label'] }}
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $cat['name'] }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2 pl-9">
                                @foreach($cat['refs'] as $ref)
                                    @php $isCompleted = in_array($ref, $completedRefs); @endphp
                                    <div class="w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         style="{{ $isCompleted ? 'background-color: ' . $cat['color'] . '; border-color: ' . $cat['color'] : 'border-color: #E5E7EB; background-color: white' }}"
                                         title="{{ $ref }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA DIREITA (1/3): Espaço para conteúdo futuro --}}
            <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-8flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-[#FAF7F5] border border-[#E4D5C3] flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-[#776246]">{{ __('Em breve') }}</p>
                <p class="text-xs text-[#B0977A] mt-1">{{ __('Mais informação aqui') }}</p>
            </div>

        </div>

    </div>
</x-app-layout>
