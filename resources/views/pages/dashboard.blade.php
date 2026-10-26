<x-app-layout>
    <div class="max-w-7xl mx-auto py-4 sm:py-8 px-2 sm:px-6 lg:px-8 pb-16">

        @php
            $categories = $categories ?? [
                ['label' => 'F', 'name' => 'Físico', 'color' => '#16a34a', 'refs' => ['F1', 'F2', 'F3', 'F4', 'F5', 'F6']],
                ['label' => 'A', 'name' => 'Afectivo', 'color' => '#dc2626', 'refs' => ['A1', 'A2', 'A3', 'A4', 'A5', 'A6']],
                ['label' => 'C', 'name' => 'Carácter', 'color' => '#2563eb', 'refs' => ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8']],
                ['label' => 'E', 'name' => 'Espiritual', 'color' => '#9333ea', 'refs' => ['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8']],
                ['label' => 'I', 'name' => 'Intelectual', 'color' => '#f97316', 'refs' => ['I1', 'I2', 'I3', 'I4', 'I5', 'I6', 'I7']],
                ['label' => 'S', 'name' => 'Social', 'color' => '#eab308', 'refs' => ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7']],
            ];

            $subcategorias = $subcategorias ?? [
                'F' => [
                    1 => ['title' => 'Desempenho', 'refs' => ['F1']],
                    2 => ['title' => 'Auto-conhecimento', 'refs' => ['F2', 'F3']],
                    3 => ['title' => 'Bem-estar Físico', 'refs' => ['F4', 'F5', 'F6']],
                ],
                'A' => [
                    1 => ['title' => 'Relacionamento e Sensibilidade', 'refs' => ['A1', 'A2', 'A3']],
                    2 => ['title' => 'Equilíbrio emocional', 'refs' => ['A4']],
                    3 => ['title' => 'Auto-estima', 'refs' => ['A5', 'A6']],
                ],
                'C' => [
                    1 => ['title' => 'Autonomia', 'refs' => ['C1', 'C2', 'C3']],
                    2 => ['title' => 'Responsabilidade', 'refs' => ['C4', 'C5', 'C6']],
                    3 => ['title' => 'Coerência', 'refs' => ['C7', 'C8']],
                ],
                'E' => [
                    1 => ['title' => 'Descoberta', 'refs' => ['E1', 'E2', 'E3']],
                    2 => ['title' => 'Aprofundamento', 'refs' => ['E4', 'E5', 'E6']],
                    3 => ['title' => 'Serviço', 'refs' => ['E7', 'E8']],
                ],
                'I' => [
                    1 => ['title' => 'Procura do conhecimento', 'refs' => ['I1', 'I2', 'I3']],
                    2 => ['title' => 'Resolução de problemas', 'refs' => ['I4', 'I5']],
                    3 => ['title' => 'Criatividade e Expressão', 'refs' => ['I6', 'I7']],
                ],
                'S' => [
                    1 => ['title' => 'Exercer ativamente Cidadania', 'refs' => ['S1', 'S2', 'S3']],
                    2 => ['title' => 'Solidariedade e tolerância', 'refs' => ['S4', 'S5', 'S6']],
                    3 => ['title' => 'Interação e cooperação', 'refs' => ['S7']],
                ],
            ];

            $refCategorias = $refCategorias ?? [];
            if (empty($refCategorias)) {
                foreach ($subcategorias as $dim => $cats) {
                    foreach ($cats as $catNum => $catData) {
                        foreach ($catData['refs'] as $ref) {
                            $refCategorias[$ref] = $catNum;
                        }
                    }
                }
            }

            $categoriaAtiva = (int) ($categoriaAtiva ?? (Auth::user()?->categoria_ativa ?? 1));

            $completedRefs = $completedRefs ?? \App\Models\ProgressNote::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->pluck('reference')
                ->toArray();

            $totalRefsAll = collect($categories)->sum(fn ($cat) => count($cat['refs']));
            $completedCountAll = count($completedRefs);

            // Determinar conclusão das etapas (Categorias 1, 2 e 3)
            $cat1Completa = $progressoEtapas['cat1_completa'] ?? false;
            $cat2Completa = $progressoEtapas['cat2_completa'] ?? false;
            $cat3Completa = $progressoEtapas['cat3_completa'] ?? false;

            $comunidadePercent = $comunidadePercent ?? ($cat1Completa ? 100 : 0);
            $partidaPercent    = $partidaPercent ?? ($cat2Completa ? 100 : 0);
            $servicoPercent    = $servicoPercent ?? ($cat3Completa ? 100 : 0);
            $overallPillarPercent = round(($comunidadePercent + $partidaPercent + $servicoPercent) / 3);

            $gap = 4;
            $segment = (360 - ($gap * 3)) / 3;

            // Os 3 pilares correspondem às 3 Etapas das Categorias
            // Quando a categoria é concluída, a etapa é ganha e a respetiva parte do círculo fica a VERMELHO (#DC2626)!
            $pilares = [
                [
                    'label'     => 'Comunidade',
                    'etapa'     => '1ª Etapa',
                    'cat'       => 1,
                    'percent'   => $comunidadePercent,
                    'completed' => $cat1Completa,
                    'color'     => '#DC2626',
                ],
                [
                    'label'     => 'Partida',
                    'etapa'     => '2ª Etapa',
                    'cat'       => 2,
                    'percent'   => $partidaPercent,
                    'completed' => $cat2Completa,
                    'color'     => '#DC2626',
                ],
                [
                    'label'     => 'Serviço',
                    'etapa'     => '3ª Etapa',
                    'cat'       => 3,
                    'percent'   => $servicoPercent,
                    'completed' => $cat3Completa,
                    'color'     => '#DC2626',
                ],
            ];

            $stops = [];
            $cursor = 0;
            foreach ($pilares as $p) {
                if ($p['completed']) {
                    // Categoria concluída -> Etapa ganha -> Parte fica completamente a VERMELHO!
                    $stops[] = "{$p['color']} {$cursor}deg " . ($cursor + $segment) . 'deg';
                } else {
                    // Em progresso: preenchimento suave proporcional, e o restante cinzento neutro
                    $filled = ($p['percent'] / 100) * $segment;
                    if ($filled > 0) {
                        $stops[] = "#F87171 {$cursor}deg " . ($cursor + $filled) . 'deg';
                        $stops[] = "#F0E9E1 " . ($cursor + $filled) . "deg " . ($cursor + $segment) . "deg";
                    } else {
                        $stops[] = "#F0E9E1 {$cursor}deg " . ($cursor + $segment) . "deg";
                    }
                }
                $cursor += $segment;
                $stops[] = "transparent {$cursor}deg " . ($cursor + $gap) . 'deg';
                $cursor += $gap;
            }
            $ringGradient = 'conic-gradient(from -90deg, ' . implode(', ', $stops) . ')';

            $monthEvents = $monthEvents ?? [];
            $calendarUrl = $calendarUrl ?? null;

            $mesesNomes = [
                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro',
            ];
            $diasSemanaCurtos = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
            $diasSemanaLongos = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];

            $hoje = \Illuminate\Support\Carbon::today();
            $mesReferencia = $mesReferencia ?? $hoje->copy();
            $inicioMes = $mesReferencia->copy()->startOfMonth();
            $fimMes = $mesReferencia->copy()->endOfMonth();

            $eventosPorDia = [];
            foreach ($monthEvents as $evento) {
                $chave = $evento['start']->format('Y-m-d');
                $eventosPorDia[$chave][] = $evento;
            }

            $inicioGrelha = $inicioMes->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $fimGrelha = $fimMes->copy()->endOfWeek(\Carbon\Carbon::MONDAY);

            $semanas = [];
            $diaAtual = $inicioGrelha->copy();
            while ($diaAtual <= $fimGrelha) {
                $semana = [];
                for ($i = 0; $i < 7; $i++) {
                    $semana[] = $diaAtual->copy();
                    $diaAtual->addDay();
                }
                $semanas[] = $semana;
            }

            $agendaDoMes = [];
            foreach ($eventosPorDia as $chave => $eventosDoDia) {
                $dataObj = \Illuminate\Support\Carbon::parse($chave);
                $agendaDoMes[] = [
                    'label'  => $diasSemanaLongos[$dataObj->dayOfWeekIso - 1] . ', ' . $dataObj->format('j') . ' de ' . $mesesNomes[$dataObj->format('m')],
                    'eventos' => $eventosDoDia,
                ];
            }

            $urlBase = request()->url();
            $mesAnteriorQS   = $mesReferencia->copy()->subMonth()->format('Y-m');
            $mesSeguinteQS   = $mesReferencia->copy()->addMonth()->format('Y-m');
            $anoAnteriorQS   = $mesReferencia->copy()->subYear()->format('Y-m');
            $anoSeguinteQS   = $mesReferencia->copy()->addYear()->format('Y-m');
            $estaNoMesAtual  = $mesReferencia->format('Y-m') === $hoje->format('Y-m');
        @endphp

        {{-- CABEÇALHO COM SELETOR DE CATEGORIA DO CLÃ --}}
        <div class="mb-6 px-2 sm:px-0 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#3E2D1B]">O meu Sistema de Progresso</h1>
                <p class="text-xs sm:text-sm text-[#776246] mt-1">Acompanha a tua caminhada no Clã, pilar a pilar e dimensão a dimensão.</p>
            </div>

            {{-- Select para escolher a categoria a trabalhar --}}
            <div class="flex items-center gap-2.5 bg-white border border-[#E4D5C3] px-3.5 py-2 rounded-2xl shadow-xs self-start md:self-auto">
                <label for="select-categoria-cla" class="text-xs font-bold text-[#776246] uppercase tracking-wider whitespace-nowrap">
                    Categoria a trabalhar:
                </label>
                <select id="select-categoria-cla"
                        onchange="mudarCategoriaCla(this.value)"
                        class="text-xs font-bold text-[#3E2D1B] bg-[#FAF7F5] border border-[#E4D5C3] rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-[#776246] focus:border-[#776246] cursor-pointer">
                    <option value="1" {{ $categoriaAtiva === 1 ? 'selected' : '' }}>
                        Categoria 1 (1ª Etapa - Comunidade) {{ $cat1Completa ? '✓' : '' }}
                    </option>
                    <option value="2" {{ $categoriaAtiva === 2 ? 'selected' : '' }}>
                        Categoria 2 (2ª Etapa - Partida) {{ $cat2Completa ? '✓' : '' }}
                    </option>
                    <option value="3" {{ $categoriaAtiva === 3 ? 'selected' : '' }}>
                        Categoria 3 (3ª Etapa - Serviço) {{ $cat3Completa ? '✓' : '' }}
                    </option>
                </select>
                <span id="categoria-cla-loading" class="hidden text-xs text-[#DC2626] font-semibold animate-pulse">A guardar...</span>
            </div>
        </div>

        {{-- LINHA DE TOPO: Círculo + Dados do utilizador --}}
        <div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-8 mb-6 flex flex-col md:flex-row items-center gap-6 sm:gap-8">

            {{-- Círculo de Progresso --}}
            <div class="flex flex-col items-center shrink-0 w-full md:w-auto">
                <div class="relative w-48 h-48 sm:w-64 sm:h-64 rounded-full shadow-lg" style="background: {{ $ringGradient }};">
                    <div class="absolute inset-6 sm:inset-8 bg-white rounded-full flex items-center justify-center">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-[#FAF7F5] border-4 border-[#F2ECE7] flex flex-col items-center justify-center shadow-inner overflow-hidden">
                            <img src="{{ asset('images/caminho.png') }}" alt="Caminho" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-white border border-[#E4D5C3] rounded-full px-3 py-0.5 sm:px-4 sm:py-1 shadow-sm">
                        <span class="text-xs sm:text-sm font-bold text-[#3E2D1B]">{{ $overallPillarPercent }}%</span>
                    </div>
                </div>

                {{-- Pilares / Etapas do Progresso --}}
                <div class="flex gap-2.5 sm:gap-3 mt-6 flex-wrap justify-center">
                    @foreach($pilares as $p)
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border {{ $p['completed'] ? 'bg-red-50 border-red-300' : ($p['cat'] === $categoriaAtiva ? 'bg-amber-50/70 border-amber-300' : 'bg-[#FAF7F5] border-[#E4D5C3]') }}"
                             title="{{ $p['label'] }} ({{ $p['etapa'] }}): {{ $p['percent'] }}% {{ $p['completed'] ? '— Concluída' : '' }}">
                            <div class="w-3 h-3 rounded-full shrink-0 flex items-center justify-center {{ $p['completed'] ? 'bg-[#DC2626]' : ($p['percent'] > 0 ? 'bg-[#F87171]' : 'bg-gray-300') }}">
                                @if($p['completed'])
                                    <svg class="w-2 h-2 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">{{ $p['label'] }}</span>
                            <span class="text-[10px] sm:text-xs {{ $p['completed'] ? 'text-[#DC2626] font-bold' : 'text-[#B0977A]' }}">{{ $p['percent'] }}%</span>
                            @if($p['completed'])
                                <span class="text-[9px] font-black bg-[#DC2626] text-white px-1.5 py-0.2 rounded-full uppercase tracking-wider">Etapa Concluída</span>
                            @elseif($p['cat'] === $categoriaAtiva)
                                <span class="text-[9px] font-bold bg-amber-500 text-white px-1.5 py-0.2 rounded-full uppercase tracking-wider">Em curso</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="hidden md:block w-px self-stretch bg-[#E4D5C3]"></div>

            {{-- Dados do utilizador --}}
            <div class="flex-1 w-full">
                <h2 class="text-lg sm:text-xl font-bold text-[#3E2D1B] mb-4 text-center md:text-left">Os meus dados</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">

                    <div class="bg-[#FAF7F5] p-3 sm:p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div class="min-w-0 pr-2">
                            <p class="text-[10px] sm:text-xs text-[#776246] uppercase font-bold tracking-wider truncate">Colaborador</p>
                            <p class="text-sm sm:text-base text-[#3E2D1B] font-medium mt-0.5 truncate">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3] shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-[#FAF7F5] p-3 sm:p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div class="min-w-0 pr-2">
                            <p class="text-[10px] sm:text-xs text-[#776246] uppercase font-bold tracking-wider truncate">Cargo</p>
                            <p class="text-sm sm:text-base text-[#3E2D1B] font-medium mt-0.5 truncate">{{ Auth::user()->cargo ?? 'Não definido' }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3] shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-[#FAF7F5] p-3 sm:p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div class="min-w-0 pr-2">
                            <p class="text-[10px] sm:text-xs text-[#776246] uppercase font-bold tracking-wider truncate">Nº Cartão CP</p>
                            <p class="text-sm sm:text-base text-[#3E2D1B] font-medium mt-0.5 truncate">{{ Auth::user()->cp_card_number ?? 'Não definido' }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3] shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-9 4h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-[#FAF7F5] p-3 sm:p-4 rounded-2xl border border-[#E4D5C3] flex justify-between items-center">
                        <div class="min-w-0 pr-2">
                            <p class="text-[10px] sm:text-xs text-[#776246] uppercase font-bold tracking-wider truncate">Nº CNE</p>
                            <p class="text-sm sm:text-base text-[#3E2D1B] font-medium mt-0.5 truncate">{{ Auth::user()->cne_number ?? 'Não definido' }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#E4D5C3] shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- LINHA DO MEIO: Grid de 2 colunas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUNA ESQUERDA (2/3): Tabela de progresso por dimensão --}}
            <div class="lg:col-span-2 bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest">Detalhe por Dimensão</h3>
                        <p class="text-[11px] text-[#B0977A] mt-0.5">Apenas os objetivos da <strong>Categoria {{ $categoriaAtiva }}</strong> estão desbloqueados para trabalho.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-[#B0977A]">{{ $completedCountAll }} de {{ $totalRefsAll }} concluídos</span>
                        <a href="{{ route('allcalendar') }}" class="text-xs font-bold text-[#DC2626] hover:underline flex items-center gap-0.5">
                            Grelha →
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 sm:gap-y-5">
                    @foreach($categories as $cat)
                        @php
                            $catTotal = count($cat['refs']);
                            $catCompleted = collect($cat['refs'])->filter(fn ($r) => in_array($r, $completedRefs))->count();
                            $catPercent = $catTotal > 0 ? round(($catCompleted / $catTotal) * 100) : 0;
                        @endphp
                        <div class="flex flex-col gap-2 p-2.5 sm:p-3 rounded-2xl hover:bg-[#FAF7F5] transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex items-center justify-center w-7 h-7 rounded-md text-xs font-bold text-white shadow-sm shrink-0"
                                         style="background-color: {{ $cat['color'] }};">
                                        {{ $cat['label'] }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">{{ $cat['name'] }}</span>
                                </div>
                                <span class="text-xs font-semibold text-[#B0977A]">{{ $catCompleted }}/{{ $catTotal }}</span>
                            </div>

                            <div class="pl-9">
                                <div class="w-full h-1.5 bg-[#F2ECE7] rounded-full overflow-hidden mb-2.5">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $catPercent }}%; background-color: {{ $cat['color'] }};"></div>
                                </div>
                                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                    @foreach($cat['refs'] as $ref)
                                        @php
                                            $isCompleted = in_array($ref, $completedRefs);
                                            $refCat = $refCategorias[$ref] ?? 1;
                                            $isActive = ($refCat === (int) $categoriaAtiva);
                                        @endphp
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all duration-300 {{ !$isActive && !$isCompleted ? 'opacity-30 border-dashed border-gray-400 bg-gray-100 cursor-not-allowed' : '' }}"
                                             style="{{ $isCompleted ? 'background-color: ' . $cat['color'] . '; border-color: ' . $cat['color'] : ($isActive ? 'border-color: ' . $cat['color'] . '; background-color: white' : 'border-color: #D1D5DB; background-color: #F3F4F6') }}"
                                             title="{{ $ref }} (Cat. {{ $refCat }}){{ $isCompleted ? ' — aprovado' : ($isActive ? ' — por realizar (ativa)' : ' — bloqueado (Cat. ' . $refCat . ')') }}">
                                            @if($isCompleted)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                                                </svg>
                                            @elseif(!$isActive)
                                                <span class="w-1 h-1 rounded-full bg-gray-400"></span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA DIREITA (1/3): Como funciona / legenda --}}
            <div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6 flex flex-col">
                <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest mb-4">Como funciona</h3>

                <p class="text-xs sm:text-sm text-[#3E2D1B] leading-relaxed mb-4">
                    Cada pilar — <strong>Comunidade</strong>, <strong>Partida</strong> e <strong>Serviço</strong> —
                    representa uma etapa do teu Caminho correspondente a uma <strong>Categoria</strong>.
                    Ao completares todos os objetivos de uma categoria em todas as dimensões, ganhas essa etapa e a respetiva parte do círculo fica a <strong>vermelho</strong>.
                </p>

                <div class="space-y-2 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" style="background-color:#DC2626;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs text-[#776246]">Objetivo aprovado</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full border-2 border-[#16a34a] bg-white shrink-0"></div>
                        <span class="text-xs text-[#776246]">Disponível (Categoria {{ $categoriaAtiva }} ativa)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full border-2 border-dashed border-gray-400 bg-gray-100 flex items-center justify-center shrink-0 opacity-70">
                            <span class="w-1 h-1 rounded-full bg-gray-400"></span>
                        </div>
                        <span class="text-xs text-[#776246]">Bloqueado (outra categoria)</span>
                    </div>
                </div>

                <details class="mt-auto border-t border-[#E4D5C3] pt-4 group">
                    <summary class="cursor-pointer text-xs sm:text-sm font-semibold text-[#3E2D1B] flex items-center justify-between list-none [&::-webkit-details-marker]:hidden">
                        Ver as 6 dimensões
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#776246] transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <ul class="mt-3 space-y-2">
                        @foreach($categories as $cat)
                            <li class="flex items-center gap-2 text-xs text-[#776246]">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $cat['color'] }};"></span>
                                {{ $cat['name'] }}
                            </li>
                        @endforeach
                    </ul>
                </details>
            </div>

        </div>

        {{-- LINHA DE BAIXO: Calendário do Clã (Google Calendar) --}}
        <div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6 mt-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest">Calendário do Clã</h3>

                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    {{-- Navegação de ano/mês --}}
                    <div class="flex items-center gap-0.5 bg-[#FAF7F5] border border-[#E4D5C3] rounded-full p-1 mx-auto sm:mx-0">
                        <a href="{{ $urlBase }}?mes={{ $anoAnteriorQS }}" title="Ano anterior"
                           class="p-1 rounded-full text-[#776246] hover:bg-white hover:text-[#3E2D1B] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 19l-7-7 7-7M11 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <a href="{{ $urlBase }}?mes={{ $mesAnteriorQS }}" title="Mês anterior"
                           class="p-1 rounded-full text-[#776246] hover:bg-white hover:text-[#3E2D1B] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <span class="text-xs sm:text-sm font-semibold text-[#3E2D1B] px-1.5 w-28 sm:w-36 text-center select-none truncate">
                            {{ $mesesNomes[$mesReferencia->format('m')] }} {{ $mesReferencia->format('Y') }}
                        </span>

                        <a href="{{ $urlBase }}?mes={{ $mesSeguinteQS }}" title="Mês seguinte"
                           class="p-1 rounded-full text-[#776246] hover:bg-white hover:text-[#3E2D1B] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ $urlBase }}?mes={{ $anoSeguinteQS }}" title="Ano seguinte"
                           class="p-1 rounded-full text-[#776246] hover:bg-white hover:text-[#3E2D1B] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 5l7 7-7 7M13 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    @unless($estaNoMesAtual)
                        <a href="{{ $urlBase }}" class="text-xs font-semibold text-[#DC2626] hover:underline shrink-0">Hoje</a>
                    @endunless

                    @if(!empty($calendarUrl))
                        <a href="{{ $calendarUrl }}" target="_blank" rel="noopener"
                           class="text-xs font-semibold text-[#B0977A] hover:text-[#3E2D1B] transition-colors shrink-0">
                            Ver completo →
                        </a>
                    @endif
                </div>
            </div>

            {{-- Cabeçalho dos dias da semana --}}
            <div class="grid grid-cols-7 gap-1 mb-1">
                @foreach($diasSemanaCurtos as $dia)
                    <div class="text-center text-[9px] sm:text-[10px] font-bold text-[#B0977A] uppercase py-1">{{ $dia }}</div>
                @endforeach
            </div>

            {{-- Grelha do mês --}}
            <div class="grid grid-cols-7 gap-1">
                @foreach($semanas as $semana)
                    @foreach($semana as $dia)
                        @php
                            $chaveDia = $dia->format('Y-m-d');
                            $eventosDoDia = $eventosPorDia[$chaveDia] ?? [];
                            $ehMesAtual = $dia->month === $mesReferencia->month;
                            $ehHoje = $dia->isSameDay($hoje);
                        @endphp
                        <div class="aspect-square flex flex-col items-center justify-center gap-0.5 rounded-lg sm:rounded-xl
                            {{ $ehMesAtual ? 'text-[#3E2D1B]' : 'text-[#E4D5C3]' }}
                            {{ $ehHoje ? 'bg-[#FAF7F5] border-2 border-[#DC2626] font-bold' : '' }}">
                            <span class="text-[11px] sm:text-xs">{{ $dia->format('j') }}</span>
                            @if(count($eventosDoDia) > 0)
                                <span class="flex gap-0.5">
                                    @foreach(array_slice($eventosDoDia, 0, 3) as $ev)
                                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-[#DC2626]"></span>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Agenda do mês, agrupada por dia --}}
            <div class="mt-6 border-t border-[#E4D5C3] pt-5">
                @if(empty($agendaDoMes))
                    <div class="flex flex-col items-center justify-center text-center py-6">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#FAF7F5] border border-[#E4D5C3] flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-[#776246]">Sem eventos este mês</p>
                        <p class="text-[10px] sm:text-xs text-[#B0977A] mt-1">As atividades do Clã vão aparecer aqui assim que forem marcadas no calendário.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($agendaDoMes as $dia)
                            <div>
                                <p class="text-[10px] sm:text-xs font-bold text-[#B0977A] uppercase tracking-wide mb-2">{{ $dia['label'] }}</p>
                                <div class="space-y-2">
                                    @foreach($dia['eventos'] as $evento)
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626] shrink-0"></span>
                                            <p class="text-xs sm:text-sm text-[#3E2D1B] flex-1 min-w-0 truncate">{{ $evento['title'] }}</p>
                                            <p class="text-[10px] sm:text-xs text-[#776246] shrink-0">
                                                @if($evento['all_day'])
                                                    Todo o dia
                                                @else
                                                    {{ $evento['start']->format('H:i') }}
                                                @endif
                                            </p>
                                            @if(!empty($evento['link']))
                                                <a href="{{ $evento['link'] }}" target="_blank" rel="noopener"
                                                   class="shrink-0 text-[#B0977A] hover:text-[#3E2D1B] transition-colors" title="Ver no Google Calendar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        function mudarCategoriaCla(cat) {
            const loading = document.getElementById('categoria-cla-loading');
            if (loading) loading.classList.remove('hidden');

            fetch('{{ route('cla.set-categoria') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    categoria: parseInt(cat)
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    window.location.reload();
                } else {
                    alert('Erro ao atualizar categoria');
                    if (loading) loading.classList.add('hidden');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro ao comunicar com o servidor');
                if (loading) loading.classList.add('hidden');
            });
        }
    </script>
</x-app-layout>
