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

            $refToCategory = collect($categories)->flatMap(
                fn ($c) => collect($c['refs'])->mapWithKeys(fn ($r) => [$r => $c])
            );

            $circumference = 2 * M_PI * 62;
            $progressLength = $circumference * ($percent / 100);
            $phaseBoundaries = [116, 232];

            // Atas do agrupamento (novo)
            $atas = \App\Models\Ata::with('user')->latest('dia')->get();
        @endphp

        {{-- LINHA DE TOPO: Anel de progresso + Dados do colaborador --}}
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
                        <div class="w-28 h-28 rounded-full bg-[#FAF7F5] border-4 border-[#F2ECE7] flex flex-col items-center justify-center shadow-inner overflow-hidden relative">
                            <img src="{{ asset('images/caminho.png') }}" alt="" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-white border border-[#E4D5C3] rounded-full px-3 py-0.5 flex items-baseline gap-1 shadow-sm">
                        <span class="text-sm font-bold text-[#3E2D1B]">{{ $percent }}%</span>
                        <span class="text-[9px] text-[#776246] uppercase tracking-wide">da Rota</span>
                    </div>
                </div>

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

            <div class="hidden md:block w-px self-stretch bg-[#E4D5C3]"></div>

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

        {{-- LINHA DO MEIO: Crachás por dimensão + Últimas Notas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Crachás por Dimensão</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    @foreach($categories as $cat)
                        @php
                            $doneInCat = collect($cat['refs'])->intersect($completedRefs)->count();
                            $totalInCat = count($cat['refs']);
                        @endphp
                        <div class="flex flex-col items-center text-center gap-2">
                            <div class="w-14 h-14 flex items-center justify-center font-bold text-sm shadow-sm"
                                 style="background-color: {{ $cat['color'] }}; color: {{ $cat['colorSoft'] }}; clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);">
                                {{ $doneInCat }}/{{ $totalInCat }}
                            </div>
                            <span class="text-sm font-semibold text-[#3E2D1B]">{{ $cat['name'] }}</span>

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

        {{-- LINHA DE BAIXO: Atas do Agrupamento (nova, área horizontal) --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-2.5">
                    <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest">Atas</h3>
                    <span class="text-xs font-semibold text-[#776246] bg-[#FAF7F5] border border-[#E4D5C3] rounded-full px-2 py-0.5">{{ $atas->count() }}</span>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-6 bg-[#EAF3DE] border border-[#B7D7A0] text-[#173404] text-sm rounded-xl px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Formulário para adicionar nova ata --}}
            <form method="POST" action="{{ route('atas.store') }}" enctype="multipart/form-data"
                  class="flex flex-col sm:flex-row sm:items-end gap-3 mb-6 bg-[#FAF7F5] border border-[#E4D5C3] rounded-2xl p-4">
                @csrf
                <div class="flex-1">
                    <label for="nome" class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Nome da Ata</label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
                           class="w-full rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]"
                           placeholder="ex: Reunião de Direção — Janeiro" required>
                    @error('nome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="dia" class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Dia</label>
                    <input type="date" name="dia" id="dia" value="{{ old('dia') }}"
                           class="rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]" required>
                    @error('dia') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="ficheiro" class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Ficheiro (PDF)</label>
                    <input type="file" name="ficheiro" id="ficheiro" accept="application/pdf"
                           class="text-sm text-[#3E2D1B] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-white file:border file:border-[#E4D5C3] file:text-[#776246] file:text-xs" required>
                    @error('ficheiro') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                        class="bg-[#3E2D1B] hover:bg-[#2A1F13] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Adicionar
                </button>
            </form>

            {{-- Lista de atas --}}
            <div class="overflow-x-auto -mx-2">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="text-xs font-bold text-[#776246] uppercase tracking-wider">
                        <th class="px-2 pb-3 border-b border-[#E4D5C3]">Ata</th>
                        <th class="px-2 pb-3 border-b border-[#E4D5C3] hidden sm:table-cell">Data</th>
                        <th class="px-2 pb-3 border-b border-[#E4D5C3] hidden md:table-cell">Adicionado por</th>
                        <th class="px-2 pb-3 border-b border-[#E4D5C3] text-right">Ficheiro</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($atas as $ata)
                        @php
                            $authorName = $ata->user->name ?? 'Utilizador removido';
                            $initials = collect(explode(' ', trim($authorName)))
                                ->filter()
                                ->map(fn ($w) => mb_substr($w, 0, 1))
                                ->take(2)
                                ->implode('');
                            $avatarPalette = ['#16a34a', '#dc2626', '#2563eb', '#9333ea', '#f97316', '#B5432A'];
                            $avatarColor = $avatarPalette[crc32($authorName) % count($avatarPalette)];
                        @endphp
                        <tr class="group hover:bg-[#FAF7F5] transition-colors">
                            <td class="px-2 py-3 border-b border-[#F2ECE7]">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 shrink-0 rounded-lg bg-[#FAF7F5] border border-[#E4D5C3] flex items-center justify-center group-hover:bg-white transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-[#3E2D1B] truncate">{{ $ata->nome }}</p>
                                        <p class="text-xs text-[#B0977A] sm:hidden">{{ \Illuminate\Support\Carbon::parse($ata->dia)->translatedFormat('d M Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 border-b border-[#F2ECE7] hidden sm:table-cell">
                                <span class="text-sm text-[#3E2D1B]">{{ \Illuminate\Support\Carbon::parse($ata->dia)->translatedFormat('d \d\e F \d\e Y') }}</span>
                            </td>
                            <td class="px-2 py-3 border-b border-[#F2ECE7] hidden md:table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 shrink-0 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                                         style="background-color: {{ $avatarColor }};">
                                        {{ strtoupper($initials) ?: '?' }}
                                    </div>
                                    <span class="text-sm text-[#3E2D1B]">{{ $authorName }}</span>
                                </div>
                            </td>
                            <td class="px-2 py-3 border-b border-[#F2ECE7] text-right">
                                <a href="{{ asset($ata->ficheiro) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border border-[#E4D5C3] text-[#776246] hover:bg-white hover:border-[#B5432A] hover:text-[#B5432A] transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                                    </svg>
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-[#FAF7F5] border border-[#E4D5C3] flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#776246]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-[#776246]">Ainda não há atas registadas</p>
                                    <p class="text-xs text-[#B0977A]">A primeira que adicionares aparece aqui.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
