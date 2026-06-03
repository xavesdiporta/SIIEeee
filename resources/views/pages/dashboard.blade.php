<x-app-layout>
    <div class="py-12">
        <!-- Container com altura fixa para ocupar a tela (100vh menos espaçamentos) -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-[calc(100vh-8rem)]">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[30px] h-full grid grid-cols-1 md:grid-cols-2">

                <!-- COLUNA DA ESQUERDA: Círculo e Progresso -->
                <!-- Alterado: justify-center para centralizar verticalmente tudo se houver espaço, ou usar 'space-y' para distribuir -->
                <div class="p-8 flex flex-col items-center h-full border-r border-[#E4D5C3] overflow-y-auto">

                    <!-- Bloco Superior: Título e Gráfico -->
                    <div class="flex flex-col items-center justify-center flex-grow w-full">
                        <h2 class="text-2xl font-bold text-[#3E2D1B] mb-8 text-center">O meu Sistema de Progresso</h2>

                        <div class="relative w-72 h-72 rounded-full shadow-xl shrink-0"
                             style="background: conic-gradient(from 302deg,
                                #7F1D1D 0deg 116deg,    /* Vermelho Escuro */
                                transparent 116deg 120deg,

                                #DC2626 120deg 236deg,  /* Vermelho Médio */
                                transparent 236deg 240deg,

                                #FECACA 240deg 356deg,  /* Vermelho Claro */
                                transparent 356deg 360deg
                             );">

                            <!-- Buraco do Donut (branco) -->
                            <div class="absolute inset-10 bg-white rounded-full flex items-center justify-center">
                                <!-- Círculo decorativo no centro -->
                                <div class="w-36 h-36 rounded-full bg-[#FAF7F5] border-8 border-[#F2ECE7] flex flex-col items-center justify-center shadow-inner overflow-hidden">
                                    <img src="{{ asset('images/caminho.png') }}" alt="Caminho" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>

                        <!-- Legenda do Gráfico -->
                        <div class="flex gap-6 mt-8">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#7F1D1D] shadow-sm"></div>
                                <span class="text-sm font-bold text-[#3E2D1B] uppercase tracking-wider">Comunidade</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#DC2626] shadow-sm"></div>
                                <span class="text-sm font-bold text-[#3E2D1B] uppercase tracking-wider">Partida</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#FECACA] shadow-sm"></div>
                                <span class="text-sm font-bold text-[#3E2D1B] uppercase tracking-wider">Serviço</span>
                            </div>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="w-3/4 h-px bg-[#F2ECE7] my-5"></div>

                    <!-- Bloco Inferior: Tabela de Progresso -->
                    <div class="w-full px-6 pb-28">
                        <h3 class="text-sm font-bold text-[#776246] uppercase mb-6 text-center tracking-widest">Detalhe por Dimensão</h3>

                        @php
                            $categories = [
                                ['label' => 'F', 'name' => 'Físico', 'color' => '#16a34a', 'refs' => ['F1', 'F2', 'F3', 'F4', 'F5', 'F6']],
                                ['label' => 'A', 'name' => 'Afectivo', 'color' => '#dc2626', 'refs' => ['A1', 'A2', 'A3', 'A4', 'A5', 'A6']],
                                ['label' => 'C', 'name' => 'Carácter', 'color' => '#2563eb', 'refs' => ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8']],
                                ['label' => 'E', 'name' => 'Espiritual', 'color' => '#9333ea', 'refs' => ['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8']],
                                ['label' => 'I', 'name' => 'Intelectual', 'color' => '#f97316', 'refs' => ['I1', 'I2', 'I3', 'I4', 'I5', 'I6', 'I7']],
                                ['label' => 'S', 'name' => 'Social', 'color' => '#eab308', 'refs' => ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7']],
                            ];

                            // ALTERADO: Adicionei ->where('status', 'approved')
                            $completedRefs = \App\Models\ProgressNote::where('user_id', Auth::id())
                                ->where('status', 'approved')
                                ->pluck('reference')
                                ->toArray();
                        @endphp

                        <div class="grid grid-cols-2 gap-x-12 gap-y-6 max-w-md mx-auto">
                            @foreach($categories as $cat)
                                <div class="flex flex-col gap-2">
                                    <!-- Cabeçalho da Categoria -->
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-8 h-8 rounded-md bg-opacity-20 text-xs font-bold shadow-sm"
                                             style="background-color: {{ $cat['color'] }}; color: white;">
                                            {{ $cat['label'] }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-700">{{ $cat['name'] }}</span>
                                    </div>

                                    <!-- Bolinhas -->
                                    <div class="flex flex-wrap gap-2 pl-11">
                                        @foreach($cat['refs'] as $ref)
                                            @php
                                                $isCompleted = in_array($ref, $completedRefs);
                                            @endphp
                                            <div class="w-3 h-3 rounded-full border transition-all duration-300"
                                                 style="{{ $isCompleted ? 'background-color: ' . $cat['color'] . '; border-color: ' . $cat['color'] : 'border-color: #E5E7EB' }}"
                                                 title="{{ $ref }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- COLUNA DA DIREITA: Informações Básicas -->
                <div class="p-8 flex flex-col justify-center bg-[#FAF7F5]">
                    <h3 class="text-xl font-bold text-[#3E2D1B] mb-6 px-2">Informações Básicas</h3>
                    <div class="space-y-4">
                        <!-- Card Informação 1 -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E4D5C3] flex justify-between items-center">
                            <div>
                                <p class="text-xs text-[#776246] uppercase font-bold">Colaborador</p>
                                <p class="text-lg text-[#3E2D1B] font-medium">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="bg-[#FAF7F5] p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#3E2D1B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Card Informação 2 -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E4D5C3] flex justify-between items-center">
                            <div>
                                <p class="text-xs text-[#776246] uppercase font-bold">Cargo</p>
                                <p class="text-lg text-[#3E2D1B] font-medium">
                                    {{ Auth::user()->cargo ?? 'Não definido' }}
                                </p>
                            </div>
                            <div class="bg-[#FAF7F5] p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#3E2D1B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
