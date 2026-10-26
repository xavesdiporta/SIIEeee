<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-4 sm:py-8 px-3 sm:px-8 lg:px-10">

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 text-[#7F1D1D] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                    Gestão · Clã
                </div>
                <h1 class="text-xl font-bold text-[#3E2D1B]">Progresso dos Caminheiros</h1>
                <p class="text-xs sm:text-sm text-[#776246] mt-0.5">{{ count($caminheiros) }} caminheiros registados</p>
            </div>
            <a href="{{ route('allcalendar') }}?view=grelha"
               class="inline-flex items-center gap-2 bg-white border border-[#E4D5C3] hover:bg-[#FAF7F5] text-[#3E2D1B] text-xs font-semibold px-3 py-2 rounded-xl transition-colors self-start sm:self-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Ver Grelha de Objetivos
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-red-50 border border-red-200 text-[#7F1D1D] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        {{-- CARDS DE RESUMO --}}
        @php
            $totalCaminheiros = count($caminheiros);
            $comMerito = collect($progressoEtapas)->filter(fn ($p) => ($p['etapas_concluidas'] ?? 0) >= 3)->count();
            $naEtapa2  = collect($progressoEtapas)->filter(fn ($p) => ($p['etapas_concluidas'] ?? 0) == 2)->count();
            $naEtapa1  = collect($progressoEtapas)->filter(fn ($p) => ($p['etapas_concluidas'] ?? 0) == 1)->count();
            $emCurso   = collect($progressoEtapas)->filter(fn ($p) => ($p['etapas_concluidas'] ?? 0) == 0)->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <div class="bg-white border border-[#E4D5C3] rounded-2xl p-4 flex flex-col gap-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-[#B0977A]">Em Curso</p>
                <p class="text-2xl font-black text-[#3E2D1B]">{{ $emCurso }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                    <span class="text-[10px] text-[#776246] font-medium">Etapa 1 em progresso</span>
                </div>
            </div>
            <div class="bg-white border border-[#E4D5C3] rounded-2xl p-4 flex flex-col gap-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-[#B0977A]">Etapa 1 Concluída</p>
                <p class="text-2xl font-black text-emerald-700">{{ $naEtapa1 }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-[10px] text-[#776246] font-medium">Comunidade</span>
                </div>
            </div>
            <div class="bg-white border border-[#E4D5C3] rounded-2xl p-4 flex flex-col gap-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-[#B0977A]">Etapa 2 Concluída</p>
                <p class="text-2xl font-black text-blue-700">{{ $naEtapa2 }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-[10px] text-[#776246] font-medium">Partida</span>
                </div>
            </div>
            <div class="bg-white border border-[#E4D5C3] rounded-2xl p-4 flex flex-col gap-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-[#B0977A]">Mérito (3 Etapas)</p>
                <p class="text-2xl font-black text-amber-700">{{ $comMerito }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] text-[#776246] font-medium">Insígnia de Mérito</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest">Tabela de Progresso</h3>
                <button type="button" onclick="document.getElementById('form-novo-caminheiro').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 bg-[#3E2D1B] hover:bg-[#2D1E0F] text-white text-xs font-semibold px-3 py-2 rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Adicionar Caminheiro
                </button>
            </div>

            {{-- FORMULÁRIO --}}
            <form id="form-novo-caminheiro" method="POST" action="{{ route('cla.store-user') }}"
                  class="hidden flex flex-col sm:flex-row sm:items-end gap-3 mb-6 bg-[#FAF7F5] border border-[#E4D5C3] rounded-2xl p-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Nome do Caminheiro</label>
                    <input type="text" name="nome" required
                           class="w-full rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#DC2626] focus:ring-[#DC2626]">
                </div>
                <button type="submit" class="bg-[#DC2626] hover:bg-[#B91C1C] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Adicionar
                </button>
            </form>

            {{-- VISTA MOBILE EM CARTÕES --}}
            <div class="block md:hidden space-y-3 mb-4">
                @forelse($caminheiros as $caminheiro)
                    @php
                        $refsDoUser = $matriz[$caminheiro->id] ?? [];
                        $totalUser = count($refsDoUser);
                        $initials = collect(explode(' ', trim($caminheiro->name)))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                        $pEtapas = $progressoEtapas[$caminheiro->id] ?? null;
                        $etapasConcluidas = $pEtapas['etapas_concluidas'] ?? 0;
                        $catAtiva = $caminheiro->categoria_ativa ?? 1;

                        $cat1pct = $pEtapas['total_por_cat'][1] > 0 ? round(($pEtapas['concluidos_por_cat'][1] / $pEtapas['total_por_cat'][1]) * 100) : 0;
                        $cat2pct = $pEtapas['total_por_cat'][2] > 0 ? round(($pEtapas['concluidos_por_cat'][2] / $pEtapas['total_por_cat'][2]) * 100) : 0;
                        $cat3pct = $pEtapas['total_por_cat'][3] > 0 ? round(($pEtapas['concluidos_por_cat'][3] / $pEtapas['total_por_cat'][3]) * 100) : 0;
                    @endphp
                    <div x-data="{ open: false }" class="border border-[#E4D5C3] bg-[#FAF7F5] rounded-2xl p-3.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#3E2D1B] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper($initials) ?: '?' }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#3E2D1B]">{{ $caminheiro->name }}</h4>
                                    <p class="text-xs text-[#776246] font-medium mt-0.5">{{ $totalUser }}/{{ $totalRefsAll }} objetivos</p>
                                    <div class="mt-1">
                                        @if($etapasConcluidas >= 3)
                                            <span class="inline-flex items-center gap-1 text-amber-800 bg-amber-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200">🏆 Mérito</span>
                                        @elseif($etapasConcluidas == 2)
                                            <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-200">⭐ Etapa 2 — Partida</span>
                                        @elseif($etapasConcluidas == 1)
                                            <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">✓ Etapa 1 — Comunidade</span>
                                        @else
                                            <span class="text-gray-400 text-[10px]">Etapa 1 em curso ({{ $cat1pct }}%)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div @click="open = !open" class="flex items-center gap-1 cursor-pointer shrink-0 ml-2">
                                <span class="text-[10px] uppercase font-bold text-[#776246]" x-text="open ? 'Fechar' : 'Detalhe'"></span>
                                <div class="w-6 h-6 rounded-full bg-white border border-[#E4D5C3] flex items-center justify-center text-[#776246] transition-transform duration-200" :class="{ 'rotate-180': open }">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Barra de progresso compacta --}}
                        <div class="mt-3 grid grid-cols-3 gap-2">
                            @foreach([
                                ['label' => 'Comunidade', 'pct' => $cat1pct, 'done' => $pEtapas['cat1_completa'] ?? false, 'color' => 'bg-[#DC2626]', 'etapa' => '1ª'],
                                ['label' => 'Partida', 'pct' => $cat2pct, 'done' => $pEtapas['cat2_completa'] ?? false, 'color' => 'bg-[#DC2626]', 'etapa' => '2ª'],
                                ['label' => 'Serviço', 'pct' => $cat3pct, 'done' => $pEtapas['cat3_completa'] ?? false, 'color' => 'bg-[#DC2626]', 'etapa' => '3ª'],
                            ] as $pilar)
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[9px] font-bold text-[#776246] uppercase">{{ $pilar['etapa'] }} Etapa</span>
                                        @if($pilar['done'])
                                            <span class="text-[9px] text-[#DC2626] font-black">✓</span>
                                        @endif
                                    </div>
                                    <div class="w-full h-1.5 bg-[#F2ECE7] rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $pilar['color'] }} transition-all" style="width: {{ $pilar['pct'] }}%;"></div>
                                    </div>
                                    <span class="text-[9px] text-[#B0977A] text-center">{{ $pilar['pct'] }}%</span>
                                </div>
                            @endforeach
                        </div>

                        <div x-show="open" x-cloak class="mt-4 pt-3 border-t border-[#E4D5C3] space-y-3">
                            @foreach($categorias as $cat)
                                <div class="bg-white rounded-xl p-3 border border-[#E4D5C3]">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-5 h-5 rounded flex items-center justify-center text-white text-[10px] font-bold" style="background-color: {{ $cat['color'] }};">{{ $cat['label'] }}</span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider" style="color: {{ $cat['color'] }};">{{ $cat['name'] }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($cat['refs'] as $ref)
                                            @php
                                                $marcado = in_array($ref, $refsDoUser, true);
                                                $catDoRef = $refCategorias[$ref] ?? 1;
                                            @endphp
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold border transition-all
                                                {{ $marcado ? 'text-white border-transparent shadow-sm' : 'text-gray-400 border-gray-200 bg-gray-50' }}"
                                                 style="{{ $marcado ? 'background-color: ' . $cat['color'] . ';' : '' }}"
                                                 title="{{ $ref }}{{ $marcado ? ' — Concluído' : ' — Por realizar (Cat. ' . $catDoRef . ')' }}">
                                                @if($marcado)
                                                    <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    {{ $ref }}
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#776246] text-center py-6">Ainda não há caminheiros registados.</p>
                @endforelse
            </div>

            {{-- TABELA DESKTOP --}}
            <div class="hidden md:block overflow-auto -mx-2 max-h-[680px] border border-[#E4D5C3] rounded-xl">
                <table class="border-collapse text-sm min-w-full">
                    <thead>
                        <tr>
                            <th rowspan="2" class="sticky top-0 left-0 z-30 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-4 py-3 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[180px]">
                                Caminheiro
                            </th>
                            <th rowspan="2" class="sticky top-0 z-20 bg-[#FAF7F5] border-b border-r-2 border-[#E4D5C3] px-3 py-3 text-center text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[200px]">
                                Etapa de Progresso
                            </th>
                            <th rowspan="2" class="sticky top-0 z-20 bg-[#FAF7F5] border-b border-r-2 border-[#E4D5C3] px-3 py-3 text-center text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[90px]">
                                Total
                            </th>
                            @foreach($categorias as $cat)
                                <th colspan="{{ count($cat['refs']) }}"
                                    class="sticky top-0 z-10 border-b border-r-2 border-[#E4D5C3] px-2 py-2 text-center text-[10px] font-bold uppercase tracking-wide"
                                    style="background-color: {{ $cat['color'] }}18; color: {{ $cat['color'] }};">
                                    {{ $cat['name'] }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($categorias as $cat)
                                @foreach($cat['refs'] as $ref)
                                    @php $catDoRef = $refCategorias[$ref] ?? 1; @endphp
                                    <th class="sticky z-10 bg-[#FDFCFB] border-b border-r border-[#E4D5C3] px-1 py-1.5 text-center w-9" style="top: 41px;">
                                        <div class="text-[10px] font-semibold text-[#776246]">{{ $ref }}</div>
                                        <div class="text-[8px] font-bold text-gray-400 mt-0.5">C{{ $catDoRef }}</div>
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($caminheiros as $caminheiro)
                        @php
                            $refsDoUser = $matriz[$caminheiro->id] ?? [];
                            $totalUser = count($refsDoUser);
                            $pEtapas = $progressoEtapas[$caminheiro->id] ?? null;
                            $etapasConcluidas = $pEtapas['etapas_concluidas'] ?? 0;
                            $catAtiva = $caminheiro->categoria_ativa ?? 1;
                            $cat1pct = ($pEtapas['total_por_cat'][1] ?? 0) > 0 ? round(($pEtapas['concluidos_por_cat'][1] / $pEtapas['total_por_cat'][1]) * 100) : 0;
                            $cat2pct = ($pEtapas['total_por_cat'][2] ?? 0) > 0 ? round(($pEtapas['concluidos_por_cat'][2] / $pEtapas['total_por_cat'][2]) * 100) : 0;
                            $cat3pct = ($pEtapas['total_por_cat'][3] ?? 0) > 0 ? round(($pEtapas['concluidos_por_cat'][3] / $pEtapas['total_por_cat'][3]) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-[#FAF7F5] transition-colors group">
                            {{-- Nome --}}
                            <td class="sticky left-0 z-10 bg-white group-hover:bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-4 py-2.5 transition-colors">
                                <div class="flex items-center gap-2.5">
                                    @php $initials = collect(explode(' ', trim($caminheiro->name)))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode(''); @endphp
                                    <div class="w-8 h-8 rounded-full bg-[#3E2D1B] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper($initials) ?: '?' }}
                                    </div>
                                    <span class="text-sm font-semibold text-[#3E2D1B] whitespace-nowrap">{{ $caminheiro->name }}</span>
                                </div>
                            </td>

                            {{-- Etapa de Progresso --}}
                            <td class="border-b border-r-2 border-[#E4D5C3] px-3 py-2.5 bg-white/70 group-hover:bg-[#FAF7F5] transition-colors">
                                <div class="flex flex-col gap-1.5">
                                    {{-- Badge de Etapa --}}
                                    <div class="flex items-center justify-center">
                                        @if($etapasConcluidas >= 3)
                                            <span class="inline-flex items-center gap-1 text-amber-800 bg-amber-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200">🏆 Mérito Completo</span>
                                        @elseif($etapasConcluidas == 2)
                                            <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-200">⭐ 2ª Etapa — Partida</span>
                                        @elseif($etapasConcluidas == 1)
                                            <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">✓ 1ª Etapa — Comunidade</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[#776246] bg-[#FAF7F5] text-[10px] font-semibold px-2 py-0.5 rounded-full border border-[#E4D5C3]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                A trabalhar Cat. {{ $catAtiva }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- 3 mini-barras das etapas --}}
                                    <div class="grid grid-cols-3 gap-1">
                                        @foreach([
                                            ['pct' => $cat1pct, 'done' => $pEtapas['cat1_completa'] ?? false, 'label' => 'C1'],
                                            ['pct' => $cat2pct, 'done' => $pEtapas['cat2_completa'] ?? false, 'label' => 'C2'],
                                            ['pct' => $cat3pct, 'done' => $pEtapas['cat3_completa'] ?? false, 'label' => 'C3'],
                                        ] as $bar)
                                            <div class="flex flex-col items-center gap-0.5">
                                                <div class="w-full h-1.5 bg-[#F2ECE7] rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full transition-all {{ $bar['done'] ? 'bg-[#DC2626]' : 'bg-[#F87171]' }}"
                                                         style="width: {{ $bar['pct'] }}%;"></div>
                                                </div>
                                                <span class="text-[8px] font-bold {{ $bar['done'] ? 'text-[#DC2626]' : 'text-gray-400' }}">
                                                    {{ $bar['label'] }}{{ $bar['done'] ? ' ✓' : ' ' . $bar['pct'] . '%' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            {{-- Total --}}
                            <td class="border-b border-r-2 border-[#E4D5C3] px-2 py-2.5 text-center font-bold text-[#3E2D1B] whitespace-nowrap bg-white/50 group-hover:bg-[#FAF7F5] transition-colors text-xs">
                                <span class="text-base font-black {{ $totalUser == $totalRefsAll ? 'text-[#DC2626]' : 'text-[#3E2D1B]' }}">{{ $totalUser }}</span>
                                <span class="text-[10px] text-[#B0977A]">/{{ $totalRefsAll }}</span>
                            </td>

                            {{-- Checkboxes por ref (READ-ONLY para chefe - só visualização) --}}
                            @foreach($categorias as $cat)
                                @foreach($cat['refs'] as $ref)
                                    @php
                                        $marcado = in_array($ref, $refsDoUser, true);
                                        $catDoRef = $refCategorias[$ref] ?? 1;
                                    @endphp
                                    <td class="border-b border-r border-[#F2ECE7] text-center py-1.5 px-1 transition-colors {{ $marcado ? '' : 'bg-gray-50/40' }}"
                                        title="{{ $ref }} (Cat. {{ $catDoRef }}): {{ $marcado ? 'Concluído' : 'Por realizar' }}">
                                        @if($marcado)
                                            <div class="w-5 h-5 rounded-full mx-auto flex items-center justify-center shadow-xs"
                                                 style="background-color: {{ $cat['color'] }};">
                                                <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-5 h-5 rounded-full mx-auto border-2 border-[#E5E7EB] bg-white opacity-50"></div>
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + $totalRefsAll }}" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#B0977A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-sm text-[#776246]">Ainda não há caminheiros registados.</p>
                                    <p class="text-xs text-[#B0977A]">Usa o botão "Adicionar Caminheiro" acima.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Legenda --}}
            <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 pt-3 border-t border-[#F2ECE7]">
                <p class="text-[10px] font-bold text-[#B0977A] uppercase tracking-wider">Legenda:</p>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-full bg-[#DC2626] flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-[10px] text-[#776246]">Objetivo concluído</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-full border-2 border-[#E5E7EB] bg-white opacity-50"></div>
                    <span class="text-[10px] text-[#776246]">Por realizar</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">✓ Etapa Concluída</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center gap-1 text-amber-800 bg-amber-100 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200">🏆 Mérito</span>
                </div>
                <p class="text-[10px] text-[#B0977A] italic ml-auto">Vista apenas de leitura. Os caminheiros atualizam o seu próprio progresso.</p>
            </div>
        </div>
    </div>
</x-app-layout>
