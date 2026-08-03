<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-8 px-6 sm:px-8 lg:px-10">

        @php
            $categories = [
                ['label' => 'F', 'name' => 'Físico', 'color' => '#16a34a', 'colorSoft' => '#EAF3DE', 'colorText' => '#173404', 'refs' => ['F1', 'F2', 'F3', 'F4', 'F5', 'F6']],
                ['label' => 'A', 'name' => 'Afectivo', 'color' => '#dc2626', 'colorSoft' => '#FCEBEB', 'colorText' => '#501313', 'refs' => ['A1', 'A2', 'A3', 'A4', 'A5', 'A6']],
                ['label' => 'C', 'name' => 'Carácter', 'color' => '#2563eb', 'colorSoft' => '#E6F1FB', 'colorText' => '#042C53', 'refs' => ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8']],
                ['label' => 'E', 'name' => 'Espiritual', 'color' => '#9333ea', 'colorSoft' => '#EEEDFE', 'colorText' => '#26215C', 'refs' => ['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8']],
                ['label' => 'I', 'name' => 'Intelectual', 'color' => '#f97316', 'colorSoft' => '#FAEEDA', 'colorText' => '#412402', 'refs' => ['I1', 'I2', 'I3', 'I4', 'I5', 'I6', 'I7']],
                ['label' => 'S', 'name' => 'Social', 'color' => '#eab308', 'colorSoft' => '#FAEEDA', 'colorText' => '#412402', 'refs' => ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7']],
            ];

            $completedRefs = \App\Models\ProgressNote::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->pluck('reference')
                ->toArray();

            $totalRefs = collect($categories)->sum(fn ($c) => count($c['refs']));
            $totalDone = count($completedRefs);
            $percent = $totalRefs > 0 ? round(($totalDone / $totalRefs) * 100) : 0;

            $recentNotes = \App\Models\ProgressNote::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->latest('updated_at')
                ->take(5)
                ->get();

            // mapa rápido referência -> categoria, para colorir as notas recentes
            $refToCategory = collect($categories)->flatMap(
                fn ($c) => collect($c['refs'])->mapWithKeys(fn ($r) => [$r => $c])
            );

            // geometria do anel de progresso (uma única cor, preenche conforme % real)
            $circumference = 2 * M_PI * 62;
            $progressLength = $circumference * ($percent / 100);

            // marcas de fase (Comunidade / Partida / Serviço), fixas na estrutura da Rota
            $phaseBoundaries = [116, 232]; // graus acumulados onde cada fase termina
        @endphp

        {{-- LINHA DE TOPO: Anel de progresso + Dados do colaborador --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-8 mb-6 flex flex-col md:flex-row items-center gap-8">

            {{-- Anel de Progresso (SVG, não depende de conic-gradient) --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="relative w-48 h-48">
                    <svg viewBox="0 0 150 150" class="w-full h-full -rotate-90">
                        {{-- pista de fundo --}}
                        <circle cx="75" cy="75" r="62" fill="none" stroke="#F2ECE7" stroke-width="14" />

                        {{-- progresso real, cor única --}}
                        <circle cx="75" cy="75" r="62" fill="none" stroke="#B5432A" stroke-width="14"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $progressLength }} {{ $circumference }}" />

                        {{-- marcas das 3 fases da Rota (Comunidade / Partida / Serviço) --}}
                        @foreach($phaseBoundaries as $deg)
                            @php
                                $rad = deg2rad($deg);
                                $x1 = 75 + 55 * cos($rad); $y1 = 75 + 55 * sin($rad);
                                $x2 = 75 + 69 * cos($rad); $y2 = 75 + 69 * sin($rad);
                            @endphp
                            <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x2 }}" y2="{{ $y2 }}" stroke="#FFFFFF" stroke-width="3" />
                        @endforeach
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-28 h-28 rounded-full bg-[#FAF7F5] border-4 border-[#F2ECE7] flex flex-col items-center justify-center shadow-inner overflow-hidden relative">
                            <img src="{{ asset('images/caminho.png') }}" alt="" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-white border border-[#E4D5C3] rounded-full px-3 py-0.5 flex items-baseline gap-1 shadow-sm">
                        <span class="text-sm font-bold text-[#3E2D1B]">{{ $percent }}%</span>
                        <span class="text-[9px] text-[#776246] uppercase tracking-wide">da Rota</span>
                    </div>
                </div>

                {{-- Legenda: as 3 fases marcadas no anel --}}
                <div class="flex gap-4 mt-5">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color:#B5432A"></div>
                        <span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Comunidade</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full border-2 border-[#B5432A]"></div>
                        <span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Partida</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full border-2 border-[#E4D5C3]"></div>
                        <span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Serviço</span>
                    </div>
                </div>
            </div>

            {{-- Divisor vertical --}}
            <div class="hidden md:block w-px self-stretch bg-[#E4D5C3]"></div>

            {{-- Dados do colaborador --}}
            <div class="flex-1 w-full">
                <p class="text-xs text-[#776246] uppercase font-bold tracking-wider mb-1">Bem-vindo de volta</p>
                <h2 class="text-xl font-bold text-[#3E2D1B] mb-4">{{ Auth::user()->name }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

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

                    <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div>
                            <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Objetivos cumpridos</p>
                            <p class="text-base text-[#3E2D1B] font-medium mt-0.5">{{ $totalDone }} de {{ $totalRefs }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div>
                            <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Dimensões iniciadas</p>
                            <p class="text-base text-[#3E2D1B] font-medium mt-0.5">
                                {{ collect($categories)->filter(fn($c) => collect($c['refs'])->intersect($completedRefs)->isNotEmpty())->count() }} de {{ count($categories) }}
                            </p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- LINHA DE BAIXO: Crachás por dimensão + Notas recentes --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUNA ESQUERDA (2/3): Crachás por dimensão --}}
            <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Crachás por Dimensão</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    @foreach($categories as $cat)
                        @php
                            $doneInCat = collect($cat['refs'])->intersect($completedRefs)->count();
                            $totalInCat = count($cat['refs']);
                        @endphp
                        <div class="flex flex-col items-center text-center gap-2">
                            {{-- crachá hexagonal com contagem --}}
                            <div class="w-14 h-14 flex items-center justify-center font-bold text-sm shadow-sm"
                                 style="background-color: {{ $cat['color'] }}; color: {{ $cat['colorSoft'] }}; clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);">
                                {{ $doneInCat }}/{{ $totalInCat }}
                            </div>
                            <span class="text-sm font-semibold text-[#3E2D1B]">{{ $cat['name'] }}</span>

                            {{-- micro-indicadores individuais, com tooltip por referência --}}
                            <div class="flex flex-wrap justify-center gap-1.5 max-w-[140px]">
                                @foreach($cat['refs'] as $ref)
                                    @php $isCompleted = in_array($ref, $completedRefs); @endphp
                                    <div class="w-3.5 h-3.5 rounded-full border-2 transition-all duration-300"
                                         style="{{ $isCompleted ? 'background-color: ' . $cat['color'] . '; border-color: ' . $cat['color'] : 'border-color: #E5E7EB; background-color: white' }}"
                                         title="{{ $ref }} — {{ $isCompleted ? 'concluído' : 'por concluir' }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA DIREITA (1/3): Notas de progresso recentes --}}
            <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6 flex flex-col">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Últimas Notas</h3>

                @forelse($recentNotes as $note)
                    @php $cat = $refToCategory[$note->reference] ?? null; @endphp
                    <div class="flex items-start gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-[#F2ECE7]' : '' }}">
                        <div class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-xs font-bold"
                             style="background-color: {{ $cat['color'] ?? '#776246' }}; color: {{ $cat['colorSoft'] ?? '#FAF7F5' }};">
                            {{ $note->reference }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-[#3E2D1B] truncate">{{ $cat['name'] ?? 'Progresso' }} aprovado</p>
                            <p class="text-xs text-[#B0977A]">{{ $note->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                        <div class="w-12 h-12 rounded-full bg-[#FAF7F5] border border-[#E4D5C3] flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-[#776246]">{{ __('Ainda sem notas aprovadas') }}</p>
                        <p class="text-xs text-[#B0977A] mt-1">{{ __('Assim que o teu Chefe aprovar a primeira, aparece aqui.') }}</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>
</x-app-layout>
