<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 pb-16">

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

            // Totais gerais das 6 dimensões
            $totalRefsAll = collect($categories)->sum(fn ($cat) => count($cat['refs']));
            $completedCountAll = count($completedRefs);

            // Percentagens das 3 etapas da Expedição (Descoberta / Desafio / Conhecimento).
            $comunidadePercent = $comunidadePercent ?? 0;
            $partidaPercent    = $partidaPercent ?? 0;
            $servicoPercent    = $servicoPercent ?? 0;
            $overallPillarPercent = round(($comunidadePercent + $partidaPercent + $servicoPercent) / 3);

            $gap = 4;
            $segment = (360 - ($gap * 3)) / 3;
            // Tons de verde oficiais dos Exploradores
            $pilares = [
                ['label' => 'Descoberta',    'percent' => $comunidadePercent, 'color' => '#14532D'],
                ['label' => 'Desafio',       'percent' => $partidaPercent,    'color' => '#16A34A'],
                ['label' => 'Conhecimento',  'percent' => $servicoPercent,    'color' => '#4ADE80'],
            ];
            $stops = [];
            $cursor = 0;
            foreach ($pilares as $p) {
                $filled = ($p['percent'] / 100) * $segment;
                $stops[] = "{$p['color']} {$cursor}deg " . ($cursor + $filled) . 'deg';
                $stops[] = '#DCFCE7 ' . ($cursor + $filled) . 'deg ' . ($cursor + $segment) . 'deg';
                $cursor += $segment;
                $stops[] = "transparent {$cursor}deg " . ($cursor + $gap) . 'deg';
                $cursor += $gap;
            }
            $ringGradient = 'conic-gradient(from -90deg, ' . implode(', ', $stops) . ')';

            // Eventos do mês do calendário
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

            // Agrupa os eventos do mês por dia (Y-m-d).
            $eventosPorDia = [];
            foreach ($monthEvents as $evento) {
                $chave = $evento['start']->format('Y-m-d');
                $eventosPorDia[$chave][] = $evento;
            }

            // Grelha de semanas
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

            // Agenda por baixo do calendário
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

        {{-- CABEÇALHO --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#DCFCE7] text-[#14532D] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span>
                    Expedição · Exploradores
                </div>
                <h1 class="text-2xl font-bold text-[#14532D]">O meu Sistema de Progresso</h1>
                <p class="text-sm text-[#166534] mt-1">Acompanha a tua pista na Expedição, etapa a etapa e dimensão a dimensão.</p>
            </div>
        </div>

        {{-- LINHA DE TOPO: Círculo + Dados do utilizador --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#BBF7D0] p-8 mb-6 flex flex-col md:flex-row items-center gap-8">

            {{-- Círculo de Progresso --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="relative w-64 h-64 rounded-full shadow-lg" style="background: {{ $ringGradient }};">
                    <div class="absolute inset-8 bg-white rounded-full flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full bg-[#F0FDF4] border-4 border-[#BBF7D0] flex flex-col items-center justify-center shadow-inner overflow-hidden">
                            <img src="{{ asset('images/seccoes/escuteiros.jpg') }}" alt="Exploradores" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-white border border-[#BBF7D0] rounded-full px-4 py-1 shadow-sm">
                        <span class="text-sm font-bold text-[#14532D]">{{ $overallPillarPercent }}%</span>
                    </div>
                </div>

                <div class="flex gap-4 mt-6 flex-wrap justify-center">
                    @foreach($pilares as $p)
                        <div class="flex items-center gap-1.5" title="{{ $p['label'] }}: {{ $p['percent'] }}%">
                            <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $p['color'] }};"></div>
                            <span class="text-xs font-bold text-[#14532D] uppercase tracking-wide">{{ $p['label'] }}</span>
                            <span class="text-xs text-[#15803D]">{{ $p['percent'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="hidden md:block w-px self-stretch bg-[#BBF7D0]"></div>

            {{-- Dados do utilizador --}}
            <div class="flex-1 w-full">
                <h2 class="text-xl font-bold text-[#14532D] mb-4">Os meus dados</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="bg-[#F0FDF4] p-4 rounded-2xl border border-[#BBF7D0] flex justify-between items-center">
                        <div>
                            <p class="text-xs text-[#166534] uppercase font-bold tracking-wider">Explorador</p>
                            <p class="text-base text-[#14532D] font-medium mt-0.5">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#BBF7D0]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-[#F0FDF4] p-4 rounded-2xl border border-[#BBF7D0] flex justify-between items-center">
                        <div>
                            <p class="text-xs text-[#166534] uppercase font-bold tracking-wider">Cargo / Patrulha</p>
                            <p class="text-base text-[#14532D] font-medium mt-0.5">{{ Auth::user()->cargo ?? 'Explorador' }}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-[#BBF7D0]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- LINHA DO MEIO: Grid de 2 colunas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUNA ESQUERDA (2/3): Tabela de progresso por dimensão --}}
            <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-[#BBF7D0] p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-[#14532D] uppercase tracking-widest">Detalhe por Dimensão</h3>
                    <span class="text-xs font-semibold text-[#15803D]">{{ $completedCountAll }} de {{ $totalRefsAll }} concluídos</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                    @foreach($categories as $cat)
                        @php
                            $catTotal = count($cat['refs']);
                            $catCompleted = collect($cat['refs'])->filter(fn ($r) => in_array($r, $completedRefs))->count();
                            $catPercent = $catTotal > 0 ? round(($catCompleted / $catTotal) * 100) : 0;
                        @endphp
                        <div class="flex flex-col gap-2.5 p-3 rounded-2xl hover:bg-[#F0FDF4] transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex items-center justify-center w-7 h-7 rounded-md text-xs font-bold text-white shadow-sm shrink-0"
                                         style="background-color: {{ $cat['color'] }};">
                                        {{ $cat['label'] }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">{{ $cat['name'] }}</span>
                                </div>
                                <span class="text-xs font-semibold text-[#15803D]">{{ $catCompleted }}/{{ $catTotal }}</span>
                            </div>

                            <div class="pl-9">
                                <div class="w-full h-1.5 bg-[#DCFCE7] rounded-full overflow-hidden mb-2.5">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $catPercent }}%; background-color: {{ $cat['color'] }};"></div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($cat['refs'] as $ref)
                                        @php $isCompleted = in_array($ref, $completedRefs); @endphp
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                             style="{{ $isCompleted ? 'background-color: ' . $cat['color'] . '; border-color: ' . $cat['color'] : 'border-color: #E5E7EB; background-color: white' }}"
                                             title="{{ $ref }}{{ $isCompleted ? ' — aprovado' : ' — por realizar' }}">
                                            @if($isCompleted)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                                                </svg>
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
            <div class="bg-white rounded-[24px] shadow-sm border border-[#BBF7D0] p-6 flex flex-col">
                <h3 class="text-sm font-bold text-[#14532D] uppercase tracking-widest mb-4">Como funciona</h3>

                <p class="text-sm text-[#14532D] leading-relaxed mb-5">
                    Cada etapa — <strong>Descoberta</strong>, <strong>Desafio</strong> e <strong>Conhecimento</strong> —
                    representa uma fase da tua Pista na Expedição. Dentro de cada uma, o teu crescimento é avaliado
                    nas 6 dimensões pessoais à esquerda.
                </p>

                <div class="space-y-2.5 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" style="background-color:#16A34A;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs text-[#15803D]">Objetivo aprovado</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full border-2 border-[#E5E7EB] bg-white shrink-0"></div>
                        <span class="text-xs text-[#15803D]">Por realizar</span>
                    </div>
                </div>

                <details class="mt-auto border-t border-[#BBF7D0] pt-4 group">
                    <summary class="cursor-pointer text-sm font-semibold text-[#14532D] flex items-center justify-between list-none [&::-webkit-details-marker]:hidden">
                        Ver as 6 dimensões
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#16A34A] transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <ul class="mt-3 space-y-2">
                        @foreach($categories as $cat)
                            <li class="flex items-center gap-2 text-xs text-[#15803D]">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $cat['color'] }};"></span>
                                {{ $cat['name'] }}
                            </li>
                        @endforeach
                    </ul>
                </details>
            </div>

        </div>

        {{-- LINHA DE BAIXO: Calendário dos Exploradores --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#BBF7D0] p-6 mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h3 class="text-sm font-bold text-[#14532D] uppercase tracking-widest">Calendário da Expedição</h3>

                <div class="flex items-center gap-3">
                    {{-- Navegação de ano/mês --}}
                    <div class="flex items-center gap-0.5 bg-[#F0FDF4] border border-[#BBF7D0] rounded-full p-1">
                        <a href="{{ $urlBase }}?mes={{ $anoAnteriorQS }}" title="Ano anterior"
                           class="p-1.5 rounded-full text-[#15803D] hover:bg-white hover:text-[#14532D] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 19l-7-7 7-7M11 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <a href="{{ $urlBase }}?mes={{ $mesAnteriorQS }}" title="Mês anterior"
                           class="p-1.5 rounded-full text-[#15803D] hover:bg-white hover:text-[#14532D] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <span class="text-sm font-semibold text-[#14532D] px-2 w-36 text-center select-none">
                            {{ $mesesNomes[$mesReferencia->format('m')] }} {{ $mesReferencia->format('Y') }}
                        </span>

                        <a href="{{ $urlBase }}?mes={{ $mesSeguinteQS }}" title="Mês seguinte"
                           class="p-1.5 rounded-full text-[#15803D] hover:bg-white hover:text-[#14532D] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ $urlBase }}?mes={{ $anoSeguinteQS }}" title="Ano seguinte"
                           class="p-1.5 rounded-full text-[#15803D] hover:bg-white hover:text-[#14532D] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 5l7 7-7 7M13 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    @unless($estaNoMesAtual)
                        <a href="{{ $urlBase }}" class="text-xs font-semibold text-[#16A34A] hover:underline shrink-0">Hoje</a>
                    @endunless

                    @if(!empty($calendarUrl))
                        <a href="{{ $calendarUrl }}" target="_blank" rel="noopener"
                           class="text-xs font-semibold text-[#15803D] hover:text-[#14532D] transition-colors shrink-0">
                            Ver calendário completo →
                        </a>
                    @endif
                </div>
            </div>

            {{-- Cabeçalho dos dias da semana --}}
            <div class="grid grid-cols-7 gap-1 mb-1">
                @foreach($diasSemanaCurtos as $dia)
                    <div class="text-center text-[10px] font-bold text-[#15803D] uppercase py-1">{{ $dia }}</div>
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
                        <div class="aspect-square flex flex-col items-center justify-center gap-0.5 rounded-xl
                            {{ $ehMesAtual ? 'text-[#14532D]' : 'text-[#16A34A]/30' }}
                            {{ $ehHoje ? 'bg-[#F0FDF4] border-2 border-[#16A34A] font-bold' : '' }}">
                            <span class="text-xs">{{ $dia->format('j') }}</span>
                            @if(count($eventosDoDia) > 0)
                                <span class="flex gap-0.5">
                                    @foreach(array_slice($eventosDoDia, 0, 3) as $ev)
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A]"></span>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Agenda do mês --}}
            <div class="mt-6 border-t border-[#BBF7D0] pt-5">
                @if(empty($agendaDoMes))
                    <div class="flex flex-col items-center justify-center text-center py-6">
                        <div class="w-12 h-12 rounded-full bg-[#F0FDF4] border border-[#BBF7D0] flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-[#14532D]">Sem eventos este mês</p>
                        <p class="text-xs text-[#15803D] mt-1">As atividades da Expedição vão aparecer aqui assim que forem marcadas no calendário.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($agendaDoMes as $dia)
                            <div>
                                <p class="text-xs font-bold text-[#15803D] uppercase tracking-wide mb-2">{{ $dia['label'] }}</p>
                                <div class="space-y-2">
                                    @foreach($dia['eventos'] as $evento)
                                        <div class="flex items-center gap-3">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A] shrink-0"></span>
                                            <p class="text-sm text-[#14532D] flex-1 min-w-0 truncate">{{ $evento['title'] }}</p>
                                            <p class="text-xs text-[#15803D] shrink-0">
                                                @if($evento['all_day'])
                                                    Todo o dia
                                                @else
                                                    {{ $evento['start']->format('H:i') }}
                                                @endif
                                            </p>
                                            @if(!empty($evento['link']))
                                                <a href="{{ $evento['link'] }}" target="_blank" rel="noopener"
                                                   class="shrink-0 text-[#16A34A] hover:text-[#14532D] transition-colors" title="Ver no Google Calendar">
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
</x-app-layout>
