<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-8 px-6 sm:px-8 lg:px-10">

        @php
            // NOTA: as descrições abaixo são placeholders — substitui pelo texto real
            // de cada objetivo do vosso referencial da Rota (podes mover isto para
            // um ficheiro de config, ex: config/rota.php, para não ficar hardcoded aqui).
            $categories = [
                ['label' => 'F', 'name' => 'Físico', 'color' => '#16a34a', 'colorSoft' => '#EAF3DE',
                    'refs' => ['F1' => 'Objetivo F1', 'F2' => 'Objetivo F2', 'F3' => 'Objetivo F3', 'F4' => 'Objetivo F4', 'F5' => 'Objetivo F5', 'F6' => 'Objetivo F6']],
                ['label' => 'A', 'name' => 'Afectivo', 'color' => '#dc2626', 'colorSoft' => '#FCEBEB',
                    'refs' => ['A1' => 'Objetivo A1', 'A2' => 'Objetivo A2', 'A3' => 'Objetivo A3', 'A4' => 'Objetivo A4', 'A5' => 'Objetivo A5', 'A6' => 'Objetivo A6']],
                ['label' => 'C', 'name' => 'Carácter', 'color' => '#2563eb', 'colorSoft' => '#E6F1FB',
                    'refs' => ['C1' => 'Objetivo C1', 'C2' => 'Objetivo C2', 'C3' => 'Objetivo C3', 'C4' => 'Objetivo C4', 'C5' => 'Objetivo C5', 'C6' => 'Objetivo C6', 'C7' => 'Objetivo C7', 'C8' => 'Objetivo C8']],
                ['label' => 'E', 'name' => 'Espiritual', 'color' => '#9333ea', 'colorSoft' => '#EEEDFE',
                    'refs' => ['E1' => 'Objetivo E1', 'E2' => 'Objetivo E2', 'E3' => 'Objetivo E3', 'E4' => 'Objetivo E4', 'E5' => 'Objetivo E5', 'E6' => 'Objetivo E6', 'E7' => 'Objetivo E7', 'E8' => 'Objetivo E8']],
                ['label' => 'I', 'name' => 'Intelectual', 'color' => '#f97316', 'colorSoft' => '#FAEEDA',
                    'refs' => ['I1' => 'Objetivo I1', 'I2' => 'Objetivo I2', 'I3' => 'Objetivo I3', 'I4' => 'Objetivo I4', 'I5' => 'Objetivo I5', 'I6' => 'Objetivo I6', 'I7' => 'Objetivo I7']],
                ['label' => 'S', 'name' => 'Social', 'color' => '#eab308', 'colorSoft' => '#FAEEDA',
                    'refs' => ['S1' => 'Objetivo S1', 'S2' => 'Objetivo S2', 'S3' => 'Objetivo S3', 'S4' => 'Objetivo S4', 'S5' => 'Objetivo S5', 'S6' => 'Objetivo S6', 'S7' => 'Objetivo S7']],
            ];

            $allNotes = \App\Models\ProgressNote::where('user_id', Auth::id())->get();

            // estado mais recente de cada referência: approved | pending | rejected | null
            $refStatus = $allNotes->groupBy('reference')->map(function ($notes) {
                return $notes->sortByDesc('updated_at')->first()->status;
            });

            $completedRefs = $refStatus->filter(fn ($s) => $s === 'approved')->keys()->toArray();
            $pendingRefs = $refStatus->filter(fn ($s) => $s === 'pending')->keys()->toArray();

            $totalRefs = collect($categories)->sum(fn ($c) => count($c['refs']));
            $totalDone = count($completedRefs);
            $percent = $totalRefs > 0 ? round(($totalDone / $totalRefs) * 100) : 0;

            $recentNotes = $allNotes->sortByDesc('updated_at')->take(6);

            $refToCategory = collect($categories)->flatMap(
                fn ($c) => collect(array_keys($c['refs']))->mapWithKeys(fn ($r) => [$r => $c])
            );

            $circumference = 2 * M_PI * 62;
            $progressLength = $circumference * ($percent / 100);
            $phaseBoundaries = [116, 232];

            $statusLabels = ['approved' => 'Aprovado', 'pending' => 'Em análise', 'rejected' => 'Rejeitado'];
        @endphp

        {{-- CABEÇALHO: título + ação principal --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <p class="text-xs text-[#776246] uppercase font-bold tracking-wider mb-1">Bem-vindo de volta</p>
                <h2 class="text-xl font-bold text-[#3E2D1B]">{{ Auth::user()->name }}</h2>
            </div>
            <a href="{{ route('progress-notes.create') }}"
               class="inline-flex items-center gap-2 bg-[#3E2D1B] hover:bg-[#2A1F13] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm self-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Submeter Nova Nota
            </a>
        </div>

        {{-- LINHA DE TOPO: Anel de progresso + Estatísticas --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-8 mb-6 flex flex-col md:flex-row items-center gap-8">

            <div class="flex flex-col items-center shrink-0">
                <div class="relative w-48 h-48">
                    <svg viewBox="0 0 150 150" class="w-full h-full -rotate-90">
                        <circle cx="75" cy="75" r="62" fill="none" stroke="#F2ECE7" stroke-width="14" />
                        <circle cx="75" cy="75" r="62" fill="none" stroke="#B5432A" stroke-width="14"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $progressLength }} {{ $circumference }}" />
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
                        <div class="w-28 h-28 rounded-full bg-[#FAF7F5] border-4 border-[#F2ECE7] flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('images/caminho.png') }}" alt="" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-white border border-[#E4D5C3] rounded-full px-3 py-0.5 flex items-baseline gap-1 shadow-sm">
                        <span class="text-sm font-bold text-[#3E2D1B]">{{ $percent }}%</span>
                        <span class="text-[9px] text-[#776246] uppercase tracking-wide">da Rota</span>
                    </div>
                </div>
                <div class="flex gap-4 mt-5">
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full" style="background-color:#B5432A"></div><span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Comunidade</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full border-2 border-[#B5432A]"></div><span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Partida</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full border-2 border-[#E4D5C3]"></div><span class="text-xs font-bold text-[#3E2D1B] uppercase tracking-wide">Serviço</span></div>
                </div>
            </div>

            <div class="hidden md:block w-px self-stretch bg-[#E4D5C3]"></div>

            <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3]">
                    <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Cargo</p>
                    <p class="text-base text-[#3E2D1B] font-medium mt-0.5">{{ Auth::user()->cargo ?? 'Não definido' }}</p>
                </div>
                <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3]">
                    <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Concluídos</p>
                    <p class="text-base text-[#3E2D1B] font-medium mt-0.5">{{ $totalDone }} de {{ $totalRefs }}</p>
                </div>
                <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3]">
                    <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Em análise</p>
                    <p class="text-base text-[#3E2D1B] font-medium mt-0.5">{{ count($pendingRefs) }}</p>
                </div>
                <div class="bg-[#FAF7F5] p-4 rounded-2xl border border-[#E4D5C3]">
                    <p class="text-xs text-[#776246] uppercase font-bold tracking-wider">Por iniciar</p>
                    <p class="text-base text-[#3E2D1B] font-medium mt-0.5">{{ $totalRefs - $totalDone - count($pendingRefs) }}</p>
                </div>
            </div>
        </div>

        {{-- LINHA DE BAIXO: Dimensões (interativas) + Notas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUNA ESQUERDA: dimensões expansíveis --}}
            <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Dimensões — clica para ver os objetivos</h3>

                <div class="flex flex-col gap-3" x-data="{ open: null }">
                    @foreach($categories as $i => $cat)
                        @php
                            $doneInCat = collect(array_keys($cat['refs']))->intersect($completedRefs)->count();
                            $totalInCat = count($cat['refs']);
                        @endphp
                        <div class="border border-[#E4D5C3] rounded-2xl overflow-hidden">
                            <button type="button" @click="open = (open === {{ $i }} ? null : {{ $i }})"
                                    class="w-full flex items-center gap-3 p-3 hover:bg-[#FAF7F5] transition-colors text-left">
                                <div class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center font-bold text-xs"
                                     style="background-color: {{ $cat['color'] }}; color: {{ $cat['colorSoft'] }};">
                                    {{ $doneInCat }}/{{ $totalInCat }}
                                </div>
                                <span class="text-sm font-semibold text-[#3E2D1B] flex-1">{{ $cat['name'] }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#776246] transition-transform" :class="open === {{ $i }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open === {{ $i }}" x-collapse class="border-t border-[#E4D5C3] divide-y divide-[#F2ECE7]">
                                @foreach($cat['refs'] as $ref => $desc)
                                    @php $status = $refStatus[$ref] ?? null; @endphp
                                    <div class="flex items-center gap-3 px-4 py-2.5">
                                        <span class="text-xs font-mono text-[#776246] w-8 shrink-0">{{ $ref }}</span>
                                        <span class="text-sm text-[#3E2D1B] flex-1">{{ $desc }}</span>

                                        @if($status === 'approved')
                                            <span class="text-xs font-semibold px-2 py-1 rounded-full" style="background-color: {{ $cat['colorSoft'] }}; color: {{ $cat['color'] }};">Aprovado</span>
                                        @elseif($status === 'pending')
                                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-[#FEF3C7] text-[#92400E]">Em análise</span>
                                        @else
                                            <a href="{{ route('progress-notes.create', ['reference' => $ref]) }}"
                                               class="text-xs font-semibold px-2 py-1 rounded-full border border-[#E4D5C3] text-[#776246] hover:bg-[#FAF7F5] transition-colors">
                                                {{ $status === 'rejected' ? 'Voltar a submeter' : 'Submeter' }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA DIREITA: notas recentes (aprovadas + pendentes) --}}
            <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6 flex flex-col">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Atividade Recente</h3>

                @forelse($recentNotes as $note)
                    @php $cat = $refToCategory[$note->reference] ?? null; @endphp
                    <div class="flex items-start gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-[#F2ECE7]' : '' }}">
                        <div class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-xs font-bold"
                             style="background-color: {{ $cat['color'] ?? '#776246' }}; color: {{ $cat['colorSoft'] ?? '#FAF7F5' }};">
                            {{ $note->reference }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-[#3E2D1B] truncate">{{ $cat['name'] ?? 'Progresso' }}</p>
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded
                                    {{ $note->status === 'approved' ? 'bg-green-100 text-green-700' : ($note->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $statusLabels[$note->status] ?? $note->status }}
                                </span>
                            </div>
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
                        <p class="text-sm font-medium text-[#776246]">Ainda sem notas submetidas</p>
                        <a href="{{ route('progress-notes.create') }}" class="text-xs font-semibold text-[#B5432A] mt-2 hover:underline">Submeter a primeira &rarr;</a>
                    </div>
                @endforelse
            </div>

        </div>

    </div>
</x-app-layout>
