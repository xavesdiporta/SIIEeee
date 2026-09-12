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

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

            {{-- CLASSIFICAÇÃO POR PESSOA --}}
            <div class="lg:col-span-1 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Por Pessoa</h3>

                <div class="flex flex-col gap-5">
                    @forelse($people as $i => $person)
                        @php
                            $initials = collect(explode(' ', trim($person['name'])))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $barWidth = $maxNoites > 0 ? round(($person['total_nights'] / $maxNoites) * 100) : 0;
                        @endphp
                        <div class="flex items-start gap-3">
                            <span class="text-xs font-bold text-[#B0977A] w-4 shrink-0 mt-1">{{ $i + 1 }}</span>
                            <div class="w-8 h-8 shrink-0 rounded-full bg-[#3E2D1B] text-white flex items-center justify-center text-[11px] font-bold mt-0.5">
                                {{ strtoupper($initials) ?: '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                {{-- Total em destaque, por cima do nome --}}
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-lg font-bold text-[#B5432A] leading-none">{{ $person['total_nights'] }}</span>
                                    <span class="text-[11px] text-[#B0977A] uppercase tracking-wide">noites</span>
                                </div>
                                <span class="text-sm font-medium text-[#3E2D1B] truncate block mt-0.5">{{ $person['name'] }}</span>
                                <div class="h-1.5 bg-[#F2ECE7] rounded-full mt-2 overflow-hidden">
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

            {{-- GRELHA ESTILO GOOGLE SHEETS --}}
            <div class="lg:col-span-3 bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
                <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest mb-6">Histórico de Atividades</h3>

                <div class="overflow-auto -mx-2 max-h-[640px] border border-[#E4D5C3] rounded-xl">
                    <table class="border-collapse text-sm min-w-full">
                        <thead>
                        <tr>
                            <th class="sticky top-0 left-0 z-20 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-3 py-2 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap">Data</th>
                            <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-3 py-2 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[180px]">Atividade</th>
                            <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-3 py-2 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[140px]">Local</th>
                            <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-2 py-2 text-center text-xs font-bold text-[#776246] uppercase whitespace-nowrap">Noites</th>
                            @foreach($people as $person)
                                @php
                                    $ini = collect(explode(' ', trim($person['name'])))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                @endphp
                                <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-1 py-2 text-center text-[10px] font-bold text-[#776246] uppercase w-10" title="{{ $person['name'] }}">
                                    {{ strtoupper($ini) }}
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($activities as $act)
                            <tr class="hover:bg-[#FAF7F5] transition-colors">
                                <td class="sticky left-0 z-10 bg-white border-b border-r border-[#E4D5C3] px-3 py-2 text-[#776246] whitespace-nowrap">{{ $act['data'] }}</td>
                                <td class="border-b border-r border-[#E4D5C3] px-3 py-2 text-[#3E2D1B] font-medium">{{ $act['nome'] }}</td>
                                <td class="border-b border-r border-[#E4D5C3] px-3 py-2 text-[#3E2D1B]">{{ $act['local'] }}</td>
                                <td class="border-b border-r border-[#E4D5C3] px-2 py-2 text-center font-semibold text-[#3E2D1B]">{{ $act['noites'] }}</td>
                                @foreach($people as $person)
                                    @php $participou = in_array($person['name'], $act['participantes'], true); @endphp
                                    <td class="border-b border-r border-[#E4D5C3] text-center {{ $participou ? 'bg-[#FCEBE6]' : '' }}">
                                        @if($participou)
                                            <span class="text-[#B5432A] font-bold">✓</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + count($people) }}" class="py-10 text-center text-sm text-[#776246]">Sem atividades registadas.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
