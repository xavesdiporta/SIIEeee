<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-4 sm:py-8 px-3 sm:px-8 lg:px-10" x-data="{ mobileTab: 'matriz' }">

        @php
            $totalNoites = collect($activities)->sum(fn ($a) => $a['acantonamento'] ? 0 : $a['noites']);
            $maxNoites = collect($people_ranked)->max('total_nights') ?: 1;
        @endphp

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
            <div>
                <h2 class="text-xl font-bold text-[#3E2D1B]">Noites de Campo</h2>
                <p class="text-xs sm:text-sm text-[#776246] mt-0.5">{{ count($activities) }} atividades registadas · {{ $totalNoites }} noites no total</p>
            </div>

            <div class="flex md:hidden bg-[#FAF7F5] border border-[#E4D5C3] p-1 rounded-xl">
                <button type="button" @click="mobileTab = 'matriz'"
                        :class="mobileTab === 'matriz' ? 'bg-[#3E2D1B] text-white shadow-xs' : 'text-[#776246]'"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-colors inline-flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 21l9-18 9 18M9 21v-4a2 2 0 012-2h2a2 2 0 012 2v4" />
                    </svg>
                    <span>Atividades</span>
                </button>

                <button type="button" @click="mobileTab = 'ranking'"
                        :class="mobileTab === 'ranking' ? 'bg-[#3E2D1B] text-white shadow-xs' : 'text-[#776246]'"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-colors inline-flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a6 6 0 006-6V3H6v6a6 6 0 006 6zm0 0v3m-3 3h6M4 5h2v3a3 3 0 01-3-3V5zm16 0h-2v3a3 3 0 003-3V5z" />
                    </svg>
                    <span>Ranking</span>
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-[#EAF3DE] border border-[#B7D7A0] text-[#173404] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

            <div class="lg:col-span-1 bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6"
                 :class="{ 'hidden md:block': mobileTab !== 'ranking', 'block': mobileTab === 'ranking' }">
                <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest mb-4 sm:mb-6">Por Pessoa</h3>

                <div class="flex flex-col gap-4 sm:gap-5" id="painel-por-pessoa">
                    @forelse($people_ranked as $i => $person)
                        @php
                            $initials = collect(explode(' ', trim($person['name'])))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $barWidth = $maxNoites > 0 ? round(($person['total_nights'] / $maxNoites) * 100) : 0;
                        @endphp
                        <div class="flex items-start gap-3 pessoa-card" data-person-col="{{ $person['col'] }}">
                            <span class="text-xs font-bold text-[#B0977A] w-4 shrink-0 mt-1">{{ $i + 1 }}</span>
                            <div class="w-8 h-8 shrink-0 rounded-full bg-[#3E2D1B] text-white flex items-center justify-center text-[11px] font-bold mt-0.5">
                                {{ strtoupper($initials) ?: '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-base sm:text-lg font-bold text-[#B5432A] leading-none valor-noites">{{ $person['total_nights'] }}</span>
                                    <span class="text-[10px] sm:text-[11px] text-[#B0977A] uppercase tracking-wide">noites</span>
                                </div>
                                <span class="text-xs sm:text-sm font-medium text-[#3E2D1B] truncate block mt-0.5">{{ $person['name'] }}</span>
                                <div class="h-1.5 bg-[#F2ECE7] rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-[#B5432A] rounded-full barra-noites" style="width: {{ $barWidth }}%"></div>
                                </div>
                                <p class="text-[10px] sm:text-[11px] text-[#B0977A] mt-1"><span class="valor-atividades">{{ $person['total_activities'] }}</span> atividades</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-[#776246] text-center py-6">Sem dados de participação.</p>
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-3 bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6"
                 :class="{ 'hidden md:block': mobileTab !== 'matriz', 'block': mobileTab === 'matriz' }">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest">Histórico de Atividades</h3>
                    <button type="button" onclick="document.getElementById('form-nova-atividade').classList.toggle('hidden')"
                            class="inline-flex items-center gap-1.5 bg-[#3E2D1B] hover:bg-[#2A1F13] text-white text-xs font-semibold px-3 py-2 rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nova Atividade
                    </button>
                </div>

                <form id="form-nova-atividade" method="POST" action="{{ route('cla.noites-campo.store') }}"
                      class="hidden flex flex-col sm:flex-row sm:items-end gap-3 mb-6 bg-[#FAF7F5] border border-[#E4D5C3] rounded-2xl p-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Data</label>
                        <input type="text" name="dia" placeholder="2026/09/16" required
                               class="w-full sm:w-auto rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Nome da atividade</label>
                        <input type="text" name="nome" required
                               class="w-full rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Local</label>
                        <input type="text" name="local"
                               class="w-full rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Noites</label>
                        <input type="number" name="noites" min="0" value="1" required
                               class="w-full sm:w-20 rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#B5432A] focus:ring-[#B5432A]">
                    </div>
                    <button type="submit" class="bg-[#B5432A] hover:bg-[#96371F] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        Adicionar
                    </button>
                </form>

                <div class="block md:hidden space-y-3">
                    @forelse($activities as $act)
                        <div x-data="{ open: false }" class="border border-[#E4D5C3] bg-[#FAF7F5] rounded-2xl p-3.5">
                            <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-[#776246] uppercase bg-white px-2 py-0.5 rounded-md border border-[#E4D5C3]">
                                            {{ $act['data'] }}
                                        </span>
                                        @if($act['acantonamento'])
                                            <span class="text-[10px] font-bold text-[#B5432A] bg-red-50 px-2 py-0.5 rounded-md border border-red-200">
                                                Acantonamento
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="text-sm font-bold text-[#3E2D1B] mt-1.5">{{ $act['nome'] }}</h4>
                                    <p class="text-xs text-[#776246] mt-0.5">{{ $act['local'] ?: 'Sem local' }} ·
                                        <strong class="{{ $act['acantonamento'] ? 'line-through text-[#B0977A]' : 'text-[#B5432A]' }}">
                                            {{ $act['noites'] }} noites
                                        </strong>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[10px] uppercase font-bold text-[#776246]" x-text="open ? 'Fechar' : 'Gerir'"></span>
                                    <div class="w-6 h-6 rounded-full bg-white border border-[#E4D5C3] flex items-center justify-center text-[#776246] transition-transform duration-200"
                                         :class="{ 'rotate-180': open }">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div x-show="open" x-cloak class="mt-3 pt-3 border-t border-[#E4D5C3] space-y-2">
                                <div class="flex items-center justify-between pb-2 border-b border-[#E4D5C3]">
                                    <span class="text-[10px] font-bold text-[#776246] uppercase tracking-wider">Marcar Participantes</span>
                                    <form method="POST" action="{{ route('cla.noites-campo.destroy') }}" onsubmit="return confirm('Tem a certeza que deseja eliminar esta atividade?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="row" value="{{ $act['row'] }}">
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-lg border border-red-200 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>

                                @foreach($people as $person)
                                    @php $participou = in_array($person['col'], $act['participantes_cols'], true); @endphp
                                    <label class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-[#E4D5C3] active:bg-gray-50 cursor-pointer">
                                        <span class="text-xs font-medium text-[#3E2D1B]">{{ $person['name'] }}</span>
                                        <input type="checkbox"
                                               class="toggle-participacao w-5 h-5 accent-[#B5432A] rounded cursor-pointer"
                                               data-row="{{ $act['row'] }}"
                                               data-col="{{ $person['col'] }}"
                                               data-noites="{{ $act['acantonamento'] ? 0 : $act['noites'] }}"
                                            {{ $participou ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[#776246] text-center py-6">Sem atividades registadas.</p>
                    @endforelse
                </div>

                <div class="hidden md:block overflow-auto -mx-2 max-h-[640px] border border-[#E4D5C3] rounded-xl">
                    <table class="border-collapse text-sm min-w-full">
                        <thead>
                        <tr>
                            <th class="sticky top-0 left-0 z-20 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-3 py-2 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap">Data</th>
                            <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-3 py-2 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[180px]">Atividade</th>
                            <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-3 py-2 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[140px]">Local</th>
                            <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-2 py-2 text-center text-xs font-bold text-[#776246] uppercase whitespace-nowrap">Noites</th>
                            <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-2 py-2 text-center text-[10px] font-bold text-[#776246] uppercase whitespace-nowrap" title="Noites que não contam para o total">Acant.</th>
                            @foreach($people as $person)
                                @php
                                    $ini = collect(explode(' ', trim($person['name'])))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                @endphp
                                <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-1 py-2 text-center text-[10px] font-bold text-[#776246] uppercase w-10" title="{{ $person['name'] }}">
                                    {{ strtoupper($ini) }}
                                </th>
                            @endforeach
                            <th class="sticky top-0 right-0 z-20 bg-[#FAF7F5] border-b border-l border-[#E4D5C3] px-3 py-2 text-center text-xs font-bold text-[#776246] uppercase whitespace-nowrap">Ação</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($activities as $act)
                            <tr class="hover:bg-[#FAF7F5] transition-colors {{ $act['acantonamento'] ? 'bg-[#FAF7F5]' : '' }}">
                                <td class="sticky left-0 z-10 bg-white border-b border-r border-[#E4D5C3] px-3 py-2 text-[#776246] whitespace-nowrap">{{ $act['data'] }}</td>
                                <td class="border-b border-r border-[#E4D5C3] px-3 py-2 text-[#3E2D1B] font-medium">{{ $act['nome'] }}</td>
                                <td class="border-b border-r border-[#E4D5C3] px-3 py-2 text-[#3E2D1B]">{{ $act['local'] }}</td>
                                <td class="border-b border-r border-[#E4D5C3] px-2 py-2 text-center font-semibold {{ $act['acantonamento'] ? 'text-[#B0977A] line-through' : 'text-[#3E2D1B]' }}">{{ $act['noites'] }}</td>
                                <td class="border-b border-r border-[#E4D5C3] text-center">
                                    @if($act['acantonamento'])
                                        <span class="text-[#B5432A]" title="Não conta para o total">●</span>
                                    @endif
                                </td>
                                @foreach($people as $person)
                                    @php $participou = in_array($person['col'], $act['participantes_cols'], true); @endphp
                                    <td class="border-b border-r border-[#E4D5C3] text-center">
                                        <input type="checkbox"
                                               class="toggle-participacao w-4 h-4 accent-[#B5432A] cursor-pointer"
                                               data-row="{{ $act['row'] }}"
                                               data-col="{{ $person['col'] }}"
                                               data-noites="{{ $act['acantonamento'] ? 0 : $act['noites'] }}"
                                            {{ $participou ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                                <td class="sticky right-0 z-10 bg-white border-b border-l border-[#E4D5C3] px-2 py-2 text-center whitespace-nowrap">
                                    <form method="POST" action="{{ route('cla.noites-campo.destroy') }}" onsubmit="return confirm('Tem a certeza que deseja eliminar esta atividade?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="row" value="{{ $act['row'] }}">
                                        <button type="submit" class="text-gray-400 hover:text-[#B5432A] transition-colors p-1" title="Eliminar atividade">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 6 + count($people) }}" class="py-10 text-center text-sm text-[#776246]">Sem atividades registadas.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            let maxNoites = {{ $maxNoites }};

            document.querySelectorAll('.toggle-participacao').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const original = checkbox.checked;
                    const noites = parseInt(checkbox.dataset.noites, 10) || 0;
                    const personCol = checkbox.dataset.col;
                    checkbox.disabled = true;

                    fetch('{{ route("cla.noites-campo.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            row: parseInt(checkbox.dataset.row, 10),
                            col: parseInt(personCol, 10),
                            value: original,
                        }),
                    })
                        .then(function (res) {
                            if (!res.ok) throw new Error('Falhou');
                            checkbox.disabled = false;
                            atualizarPainelPessoa(personCol, noites, original);
                        })
                        .catch(function () {
                            checkbox.checked = !original;
                            checkbox.disabled = false;
                            alert('Não foi possível guardar. Tenta outra vez.');
                        });
                });
            });

            function atualizarPainelPessoa(personCol, noites, marcado) {
                const card = document.querySelector('.pessoa-card[data-person-col="' + personCol + '"]');
                if (!card) return;

                const valorNoites = card.querySelector('.valor-noites');
                const valorAtividades = card.querySelector('.valor-atividades');
                const barra = card.querySelector('.barra-noites');

                let novoTotalNoites = parseInt(valorNoites.textContent, 10) + (marcado ? noites : -noites);
                let novoTotalAtividades = parseInt(valorAtividades.textContent, 10) + (marcado ? 1 : -1);

                novoTotalNoites = Math.max(0, novoTotalNoites);
                novoTotalAtividades = Math.max(0, novoTotalAtividades);

                valorNoites.textContent = novoTotalNoites;
                valorAtividades.textContent = novoTotalAtividades;

                if (novoTotalNoites > maxNoites) {
                    maxNoites = novoTotalNoites;
                }

                const novaLargura = maxNoites > 0 ? Math.round((novoTotalNoites / maxNoites) * 100) : 0;
                barra.style.width = novaLargura + '%';
            }
        });
    </script>
</x-app-layout>
