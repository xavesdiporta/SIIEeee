<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-8 px-6 sm:px-8 lg:px-10">

        @php
            $totalNoites = collect($activities)->sum('noites');
            $maxNoites = collect($people)->max('total_nights') ?: 1;
        @endphp

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl font-bold text-[#3E2D1B]">Noites de Campo</h2>
                <p class="text-sm text-[#776246] mt-1">{{ count($activities) }} atividades registadas · {{ $totalNoites }} noites no total</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- CLASSIFICAÇÃO POR PESSOA --}}
            <div class="lg:col-span-1 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Por Pessoa</h3>

                <div class="flex flex-col gap-4">
                    @forelse($people as $i => $person)
                        @php
                            $initials = collect(explode(' ', trim($person['name'])))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $barWidth = $maxNoites > 0 ? round(($person['total_nights'] / $maxNoites) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-[#B0977A] w-4 shrink-0">{{ $i + 1 }}</span>
                            <div class="w-8 h-8 shrink-0 rounded-full bg-[#3E2D1B] text-white flex items-center justify-center text-[11px] font-bold">
                                {{ strtoupper($initials) ?: '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-sm font-medium text-[#3E2D1B] truncate">{{ $person['name'] }}</span>
                                    <span class="text-xs text-[#776246] shrink-0">{{ $person['total_nights'] }} noites</span>
                                </div>
                                <div class="h-1.5 bg-[#F2ECE7] rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-[#B5432A] rounded-full" style="width: {{ $barWidth }}%"></div>
                                </div>
                                <p class="text-[11px] text-[#B0977A] mt-1">{{ $person['total_activities'] }} atividades</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-[#776246] text-center py-6">Sem dados de participação.</p>
                    @endforelse
                </div>
            </div>

            {{-- HISTÓRICO DE ATIVIDADES --}}
            <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Histórico de Atividades</h3>

                <div class="overflow-x-auto -mx-2 max-h-[560px] overflow-y-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="sticky top-0 bg-white">
                        <tr class="text-xs font-bold text-[#776246] uppercase tracking-wider">
                            <th class="px-2 pb-3 border-b border-[#E4D5C3]">Data</th>
                            <th class="px-2 pb-3 border-b border-[#E4D5C3]">Atividade</th>
                            <th class="px-2 pb-3 border-b border-[#E4D5C3] hidden md:table-cell">Local</th>
                            <th class="px-2 pb-3 border-b border-[#E4D5C3] text-center">Noites</th>
                            <th class="px-2 pb-3 border-b border-[#E4D5C3]">Participantes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($activities as $act)
                            <tr class="hover:bg-[#FAF7F5] transition-colors align-top">
                                <td class="px-2 py-3 border-b border-[#F2ECE7] text-[#776246] whitespace-nowrap">{{ $act['data'] }}</td>
                                <td class="px-2 py-3 border-b border-[#F2ECE7]">
                                    <p class="text-[#3E2D1B] font-medium">{{ $act['nome'] }}</p>
                                    <p class="text-xs text-[#B0977A] md:hidden">{{ $act['local'] }}</p>
                                </td>
                                <td class="px-2 py-3 border-b border-[#F2ECE7] text-[#3E2D1B] hidden md:table-cell">{{ $act['local'] }}</td>
                                <td class="px-2 py-3 border-b border-[#F2ECE7] text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#FAF7F5] border border-[#E4D5C3] text-xs font-bold text-[#3E2D1B]">
                                            {{ $act['noites'] }}
                                        </span>
                                </td>
                                <td class="px-2 py-3 border-b border-[#F2ECE7]">
                                    @if(count($act['participantes']))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($act['participantes'] as $nomeParticipante)
                                                @php
                                                    $ini = collect(explode(' ', trim($nomeParticipante)))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                                @endphp
                                                <span title="{{ $nomeParticipante }}"
                                                      class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#3E2D1B] text-white text-[10px] font-bold">
                                                        {{ strtoupper($ini) }}
                                                    </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-[#B0977A]">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-sm text-[#776246]">Sem atividades registadas.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
