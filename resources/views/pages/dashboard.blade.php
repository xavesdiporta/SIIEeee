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

            // Totais gerais das 6 dimensões — usados no cartão "Detalhe por Dimensão".
            $totalRefsAll = collect($categories)->sum(fn ($cat) => count($cat['refs']));
            $completedCountAll = count($completedRefs);

            // Percentagens dos 3 pilares do Sistema de Progresso (Comunidade / Partida / Serviço).
            // TODO: substitui isto por valores reais vindos do Controller, ex.:
            // return view('progresso.dashboard', ['comunidadePercent' => $x, 'partidaPercent' => $y, 'servicoPercent' => $z]);
            $comunidadePercent = $comunidadePercent ?? 0;
            $partidaPercent    = $partidaPercent ?? 0;
            $servicoPercent    = $servicoPercent ?? 0;
            $overallPillarPercent = round(($comunidadePercent + $partidaPercent + $servicoPercent) / 3);

            $gap = 4;
            $segment = (360 - ($gap * 3)) / 3;
            $pilares = [
                ['label' => 'Comunidade', 'percent' => $comunidadePercent, 'color' => '#7F1D1D'],
                ['label' => 'Partida',    'percent' => $partidaPercent,    'color' => '#DC2626'],
                ['label' => 'Serviço',    'percent' => $servicoPercent,    'color' => '#FCA5A5'],
            ];
            $stops = [];
            $cursor = 0;
            foreach ($pilares as $p) {
                $filled = ($p['percent'] / 100) * $segment;
                $stops[] = "{$p['color']} {$cursor}deg " . ($cursor + $filled) . 'deg';
                $stops[] = '#F0E9E1 ' . ($cursor + $filled) . 'deg ' . ($cursor + $segment) . 'deg';
                $cursor += $segment;
                $stops[] = "transparent {$cursor}deg " . ($cursor + $gap) . 'deg';
                $cursor += $gap;
            }
            $ringGradient = 'conic-gradient(from -90deg, ' . implode(', ', $stops) . ')';

            // Eventos do calendário do Clã — vêm do Controller via GoogleCalendarService.
            // Se ainda não estiverem a ser passados, a secção mostra um estado vazio em vez de rebentar.
            $events = $events ?? [];
            $calendarUrl = $calendarUrl ?? null;
            $mesesAbrev = ['01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'];
        @endphp

        {{-- CABEÇALHO --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#3E2D1B]">O meu Sistema de Progresso</h1>
            <p class="text-sm text-[#776246] mt-1">Acompanha a tua caminhada no Clã, pilar a pilar e dimensão a dimensão.</p>
        </div>

        {{-- LINHA DE TOPO: Círculo + Dados do utilizador --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-8 mb-6 flex flex-col md:flex-row items-center gap-8">

            {{-- Círculo de Progresso --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="relative w-64 h-64 rounded-full shadow-lg" style="background: {{ $ringGradient }};">
                    <div class="absolute inset-8 bg-white rounded-full flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full bg-[#FAF7F5] border-4 border-[#F2ECE7] flex flex-col items-center justify-center shadow-inner overflow-hidden">
                            <img src="{{ asset('images/caminho.png') }}" alt="Caminho" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-white border border-[#E4D5C3] rounded-full px-4 py-1 shadow-sm">
                        <span class="text-sm font-bold text-[#3E2D1B]">{{ $overallPillarPercent }}%</span>
                    </div>
                </div>

                <div class="flex gap-4 mt-6 flex-wrap justify-center">
                    @foreach($pilares as $p)
                        <div class="flex items-center gap-1.5" title="{{ $p['label'] }}: {{ $p['percent'] }}%">
                            <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $p['color'] }};"></div>
                            <span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">{{ $p['label'] }}</span>
                            <span class="text-xs text-[#B0977A]">{{ $p['percent'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="hidden md:block w-px self-stretch bg-[#E4D5C3]"></div>

            {{-- Dados do utilizador --}}
            <div class="flex-1 w-full">
                <h2 class="text-xl font-bold text-[#3E2D1B] mb-4">Os meus dados</h2>
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

        {{-- LINHA DO MEIO: Grid de 2 colunas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUNA ESQUERDA (2/3): Tabela de progresso por dimensão --}}
            <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest">Detalhe por Dimensão</h3>
                    <span class="text-xs font-semibold text-[#B0977A]">{{ $completedCountAll }} de {{ $totalRefsAll }} concluídos</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                    @foreach($categories as $cat)
                        @php
                            $catTotal = count($cat['refs']);
                            $catCompleted = collect($cat['refs'])->filter(fn ($r) => in_array($r, $completedRefs))->count();
                            $catPercent = $catTotal > 0 ? round(($catCompleted / $catTotal) * 100) : 0;
                        @endphp
                        <div class="flex flex-col gap-2.5 p-3 rounded-2xl hover:bg-[#FAF7F5] transition-colors">
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
            <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6 flex flex-col">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-4">Como funciona</h3>

                <p class="text-sm text-[#3E2D1B] leading-relaxed mb-5">
                    Cada pilar — <strong>Comunidade</strong>, <strong>Partida</strong> e <strong>Serviço</strong> —
                    representa uma etapa do teu Caminho. Dentro de cada um, o teu crescimento é avaliado
                    nas 6 dimensões pessoais à esquerda.
                </p>

                <div class="space-y-2.5 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" style="background-color:#3E2D1B;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs text-[#776246]">Objetivo aprovado</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full border-2 border-[#E5E7EB] bg-white shrink-0"></div>
                        <span class="text-xs text-[#776246]">Por realizar</span>
                    </div>
                </div>

                <details class="mt-auto border-t border-[#E4D5C3] pt-4 group">
                    <summary class="cursor-pointer text-sm font-semibold text-[#3E2D1B] flex items-center justify-between list-none [&::-webkit-details-marker]:hidden">
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

        {{-- LINHA DE BAIXO: Próximos eventos do Clã (Google Calendar) --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6 mt-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest">Próximos Eventos do Clã</h3>
                @if(!empty($calendarUrl))
                    <a href="{{ $calendarUrl }}" target="_blank" rel="noopener"
                       class="text-xs font-semibold text-[#B0977A] hover:text-[#3E2D1B] transition-colors">
                        Ver calendário completo →
                    </a>
                @endif
            </div>

            @if(empty($events))
                <div class="flex flex-col items-center justify-center text-center py-8">
                    <div class="w-12 h-12 rounded-full bg-[#FAF7F5] border border-[#E4D5C3] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-[#776246]">Sem eventos agendados de momento</p>
                    <p class="text-xs text-[#B0977A] mt-1">As próximas atividades do Clã vão aparecer aqui assim que forem marcadas no calendário.</p>
                </div>
            @else
                <div class="divide-y divide-[#F2ECE7]">
                    @foreach($events as $event)
                        <div class="flex items-center gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl bg-[#FAF7F5] border border-[#E4D5C3] shrink-0">
                                <span class="text-[10px] font-bold text-[#B0977A] uppercase leading-none">{{ $mesesAbrev[$event['start']->format('m')] }}</span>
                                <span class="text-lg font-bold text-[#3E2D1B] leading-tight mt-0.5">{{ $event['start']->format('d') }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[#3E2D1B] truncate">{{ $event['title'] }}</p>
                                <p class="text-xs text-[#776246] mt-0.5">
                                    @if($event['all_day'])
                                        Todo o dia
                                    @else
                                        {{ $event['start']->format('H:i') }} – {{ $event['end']->format('H:i') }}
                                    @endif
                                    @if(!empty($event['location']))
                                        · {{ $event['location'] }}
                                    @endif
                                </p>
                            </div>

                            @if(!empty($event['link']))
                                <a href="{{ $event['link'] }}" target="_blank" rel="noopener"
                                   class="shrink-0 text-[#B0977A] hover:text-[#3E2D1B] transition-colors" title="Ver no Google Calendar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
