<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <!-- CABEÇALHO DA PÁGINA -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-[#E4D5C3]/60 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-[#3E2D1B] tracking-tight">Painel de Desempenho</h1>
                <p class="text-sm text-[#776246] mt-1">Acompanha o teu desenvolvimento individual e dados de colaborador.</p>
            </div>
        </div>

        <!-- GRID PRINCIPAL (Assimétrico: 2 colunas para progresso, 1 para info) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- COLUNA DA ESQUERDA: SISTEMA DE PROGRESSO (Ocupa 2/3 do ecrã) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- CARD DO GRÁFICO DONUT -->
                <div class="bg-white rounded-2xl shadow-sm border border-[#E4D5C3]/50 p-6 flex flex-col items-center">
                    <h2 class="text-lg font-bold text-[#3E2D1B] mb-6 self-start px-2">O meu Sistema de Progresso</h2>

                    <div class="relative w-64 h-64 rounded-full shadow-lg shrink-0 my-4"
                         style="background: conic-gradient(from 302deg,
                            #7F1D1D 0deg 116deg,
                            transparent 116deg 120deg,
                            #DC2626 120deg 236deg,
                            transparent 236deg 240deg,
                            #FECACA 240deg 356deg,
                            transparent 356deg 360deg
                         );">

                        <!-- Buraco do Donut -->
                        <div class="absolute inset-8 bg-white rounded-full flex items-center justify-center">
                            <div class="w-32 h-32 rounded-full bg-[#FAF7F5] border-4 border-[#F2ECE7] flex items-center justify-center shadow-inner overflow-hidden">
                                <img src="{{ asset('images/caminho.png') }}" alt="Caminho" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <!-- Legenda do Gráfico Moderna -->
                    <div class="flex flex-wrap justify-center gap-6 mt-6 bg-[#FAF7F5] px-6 py-3 rounded-xl border border-[#F2ECE7]">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#7F1D1D]"></div>
                            <span class="text-xs font-semibold text-[#3E2D1B] uppercase tracking-wider">Comunidade</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#DC2626]"></div>
                            <span class="text-xs font-semibold text-[#3E2D1B] uppercase tracking-wider">Partida</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#FECACA] border border-red-300"></div>
                            <span class="text-xs font-semibold text-[#3E2D1B] uppercase tracking-wider">Serviço</span>
                        </div>
                    </div>
                </div>

                <!-- CARD DOS DETALHES POR DIMENSÃO -->
                <div class="bg-white rounded-2xl shadow-sm border border-[#E4D5C3]/50 p-6">
                    <h3 class="text-sm font-bold text-[#776246] uppercase tracking-wider mb-6">Detalhe por Dimensão</h3>

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

                        <!-- Grid Dinâmico de Dimensões -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($categories as $cat)
                            <div class="p-4 rounded-xl border border-slate-100 bg-[#FAF7F5]/50 flex items-center justify-between transition hover:shadow-sm">

                                <!-- Nome e Badge da Categoria -->
                                <div class="flex items-center gap-3">
                                    <!-- Badge com cor dinâmica e opacidade suave para look moderno -->
                                    <div class="flex items-center justify-center w-7 h-7 rounded-lg font-bold text-xs"
                                         style="background-color: {{ $cat['color'] }}20; color: {{ $cat['color'] }};">
                                        {{ $cat['label'] }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">{{ $cat['name'] }}</span>
                                </div>

                                <!-- Indicadores de Progresso (Bolinhas) -->
                                <div class="flex flex-wrap gap-1.5 justify-end max-w-[120px]">
                                    @foreach($cat['refs'] as $ref)
                                        @php $isCompleted = in_array($ref, $completedRefs); @endphp
                                        <div class="w-2.5 h-2.5 rounded-full border transition-all duration-300"
                                             style="{{ $isCompleted ? 'background-color: ' . $cat['color'] . '; border-color: ' . $cat['color'] : 'border-color: #D1D5DB; background-color: #FFFFFF;' }}"
                                             title="{{ $ref }}">
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- COLUNA DA DIREITA: INFORMAÇÕES BÁSICAS (Ocupa 1/3 do ecrã) -->
            <div class="space-y-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E4D5C3]/50">
                    <h3 class="text-sm font-bold text-[#776246] uppercase tracking-wider mb-4">Informações do Perfil</h3>

                    <div class="space-y-3">
                        <!-- Colaborador -->
                        <div class="p-4 rounded-xl bg-[#FAF7F5] border border-[#E4D5C3]/30 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] text-[#776246] uppercase font-bold tracking-wider">Colaborador</p>
                                <p class="text-base text-[#3E2D1B] font-semibold mt-0.5">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="bg-white p-2 rounded-lg text-[#3E2D1B] shadow-sm border border-[#E4D5C3]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Cargo -->
                        <div class="p-4 rounded-xl bg-[#FAF7F5] border border-[#E4D5C3]/30 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] text-[#776246] uppercase font-bold tracking-wider">Cargo</p>
                                <p class="text-base text-[#3E2D1B] font-semibold mt-0.5">{{ Auth::user()->cargo ?? 'Não definido' }}</p>
                            </div>
                            <div class="bg-white p-2 rounded-lg text-[#3E2D1B] shadow-sm border border-[#E4D5C3]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
