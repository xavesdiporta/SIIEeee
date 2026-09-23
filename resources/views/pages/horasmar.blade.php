<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-4 sm:py-8 px-3 sm:px-8 lg:px-10" x-data="{ mobileTab: 'matriz' }">

        @php
            $maxHoras = collect($people_ranked)->max('total_hours') ?: 1;
        @endphp

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
            <div>
                <h2 class="text-xl font-bold text-[#3E2D1B]">Horas de Mar</h2>
                <p class="text-xs sm:text-sm text-[#776246] mt-0.5">{{ count($activities) }} atividades registadas</p>
            </div>

            <!-- TABS DE NAVEGAÇÃO APENAS EM MOBILE -->
            <div class="flex md:hidden bg-[#FAF7F5] border border-[#E4D5C3] p-1 rounded-xl">
                <button type="button" @click="mobileTab = 'matriz'"
                        :class="mobileTab === 'matriz' ? 'bg-[#3E2D1B] text-white shadow-xs' : 'text-[#776246]'"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-colors text-center">
                    ⛵ Atividades
                </button>
                <button type="button" @click="mobileTab = 'ranking'"
                        :class="mobileTab === 'ranking' ? 'bg-[#3E2D1B] text-white shadow-xs' : 'text-[#776246]'"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-colors text-center">
                    🏆 Ranking
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-[#EAF3DE] border border-[#B7D7A0] text-[#173404] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

            {{-- CLASSIFICAÇÃO POR PESSOA --}}
            <div class="lg:col-span-1 bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6"
                 :class="{ 'hidden md:block': mobileTab !== 'ranking', 'block': mobileTab === 'ranking' }">
                <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest mb-4 sm:mb-6">Por Pessoa</h3>

                <div class="flex flex-col gap-4 sm:gap-5" id="painel-por-pessoa">
                    @forelse($people_ranked as $i => $person)
                        @php
                            $initials = collect(explode(' ', trim($person['name'])))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $barWidth = $maxHoras > 0 ? round(($person['total_hours'] / $maxHoras) * 100) : 0;
                            $horasFmt = rtrim(rtrim(number_format($person['total_hours'], 1, ',', ''), '0'), ',');
                        @endphp
                        <div class="flex items-start gap-3 pessoa-card" data-person-row="{{ $person['row'] }}">
                            <span class="text-xs font-bold text-[#B0977A] w-4 shrink-0 mt-1">{{ $i + 1 }}</span>
                            <div class="w-8 h-8 shrink-0 rounded-full bg-[#3E2D1B] text-white flex items-center justify-center text-[11px] font-bold mt-0.5">
                                {{ strtoupper($initials) ?: '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-base sm:text-lg font-bold text-[#2563EB] leading-none valor-horas" data-raw="{{ $person['total_hours'] }}">{{ $horasFmt }}</span>
                                    <span class="text-[10px] sm:text-[11px] text-[#B0977A] uppercase tracking-wide">horas</span>
                                </div>
                                <span class="text-xs sm:text-sm font-medium text-[#3E2D1B] truncate block mt-0.5">{{ $person['name'] }}</span>
                                <div class="h-1.5 bg-[#F2ECE7] rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-[#2563EB] rounded-full barra-horas" style="width: {{ $barWidth }}%"></div>
                                </div>
                                <p class="text-[10px] sm:text-[11px] text-[#B0977A] mt-1"><span class="valor-atividades">{{ $person['total_activities'] }}</span> saídas</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-[#776246] text-center py-6">Sem dados de participação.</p>
                    @endforelse
                </div>
            </div>

            {{-- ZONA PRINCIPAL DE REGISTOS --}}
            <div class="lg:col-span-3 bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#E4D5C3] p-4 sm:p-6"
                 :class="{ 'hidden md:block': mobileTab !== 'matriz', 'block': mobileTab === 'matriz' }">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs sm:text-sm font-bold text-[#776246] uppercase tracking-widest">Atividades no Mar</h3>
                    <button type="button" onclick="document.getElementById('form-nova-atividade-mar').classList.toggle('hidden')"
                            class="inline-flex items-center gap-1.5 bg-[#3E2D1B] hover:bg-[#2A1F13] text-white text-xs font-semibold px-3 py-2 rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nova Atividade
                    </button>
                </div>

                <form id="form-nova-atividade-mar" method="POST" action="{{ route('cla.horasmar.store') }}"
                      class="hidden flex flex-col sm:flex-row sm:items-end gap-3 mb-6 bg-[#FAF7F5] border border-[#E4D5C3] rounded-2xl p-4">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Nome da atividade</label>
                        <input type="text" name="nome" placeholder="ex: Down River 27" required
                               class="w-full rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#776246] uppercase tracking-wider mb-1.5">Horas</label>
                        <input type="number" name="horas" step="0.5" min="0" value="12.5" required
                               class="w-full sm:w-24 rounded-xl border-[#E4D5C3] bg-white text-[#3E2D1B] text-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                    </div>
                    <button type="submit" class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        Adicionar
                    </button>
                </form>

                <!-- VISÃO EM CARTÕES PARA MOBILE (Exibida apenas abaixo de md) -->
                <div class="block md:hidden space-y-3">
                    @forelse($activities as $act)
                        <div x-data="{ open: false }" class="border border-[#E4D5C3] bg-[#FAF7F5] rounded-2xl p-3.5">
                            <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                                <div>
                                    <h4 class="text-sm font-bold text-[#3E2D1B]">{{ $act['nome'] }}</h4>
                                    <span class="text-xs font-semibold text-[#2563EB]">
                                        {{ rtrim(rtrim(number_format($act['horas'], 1, ',', ''), '0'), ',') }} horas
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
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
                                @foreach($people as $person)
                                    @php $participou = in_array($act['col'], $person['atividades_cols'], true); @endphp
                                    <label class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-[#E4D5C3] active:bg-gray-50 cursor-pointer">
                                        <span class="text-xs font-medium text-[#3E2D1B]">{{ $person['name'] }}</span>
                                        <input type="checkbox"
                                               class="toggle-participacao-mar w-5 h-5 accent-[#2563EB] rounded cursor-pointer"
                                               data-row="{{ $person['row'] }}"
                                               data-col="{{ $act['col'] }}"
                                               data-horas="{{ $act['horas'] }}"
                                            {{ $participou ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[#776246] text-center py-6">Sem atividades registadas.</p>
                    @endforelse
                </div>

                <!-- VISÃO EM TABELA MATRIZ PARA DESKTOP (Exibida a partir de md) -->
                <div class="hidden md:block overflow-auto -mx-2 max-h-[640px] border border-[#E4D5C3] rounded-xl">
                    <table class="border-collapse text-sm min-w-full">
                        <thead>
                        <tr>
                            <th class="sticky top-0 left-0 z-20 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-3 py-2 text-left text-xs font-bold text-[#776246] uppercase whitespace-nowrap min-w-[160px]">Nome</th>
                            @foreach($activities as $act)
                                <th class="sticky top-0 z-10 bg-[#FAF7F5] border-b border-r border-[#E4D5C3] px-2 py-2 text-center text-[10px] font-bold text-[#776246] w-16" title="{{ $act['nome'] }}">
                                    <span class="block truncate max-w-[60px] mx-auto">{{ $act['nome'] }}</span>
                                    <span class="block text-[#B0977A] font-normal normal-case">{{ rtrim(rtrim(number_format($act['horas'], 1, ',', ''), '0'), ',') }}h</span>
                                </th>
                            @endforeach
                            <th class="sticky top-0 right-0 z-20 bg-[#FAF7F5] border-b border-l-2 border-[#E4D5C3] px-3 py-2 text-center text-xs font-bold text-[#776246] uppercase whitespace-nowrap">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($people as $person)
                            <tr class="hover:bg-[#FAF7F5] transition-colors">
                                <td class="sticky left-0 z-10 bg-white border-b border-r border-[#E4D5C3] px-3 py-2 text-[#3E2D1B] font-medium whitespace-nowrap">{{ $person['name'] }}</td>
                                @foreach($activities as $act)
                                    @php $participou = in_array($act['col'], $person['atividades_cols'], true); @endphp
                                    <td class="border-b border-r border-[#E4D5C3] text-center">
                                        <input type="checkbox"
                                               class="toggle-participacao-mar w-4 h-4 accent-[#2563EB] cursor-pointer"
                                               data-row="{{ $person['row'] }}"
                                               data-col="{{ $act['col'] }}"
                                               data-horas="{{ $act['horas'] }}"
                                            {{ $participou ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                                <td class="sticky right-0 z-10 bg-white border-b border-l-2 border-[#E4D5C3] px-3 py-2 text-center font-semibold text-[#3E2D1B] whitespace-nowrap">
                                    {{ rtrim(rtrim(number_format($person['total_hours'], 1, ',', ''), '0'), ',') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + count($activities) }}" class="py-10 text-center text-sm text-[#776246]">Sem pessoas registadas.</td>
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
            let maxHoras = {{ $maxHoras }};

            document.querySelectorAll('.toggle-participacao-mar').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const original = checkbox.checked;
                    const horas = parseFloat(checkbox.dataset.horas) || 0;
                    const personRow = checkbox.dataset.row;
                    checkbox.disabled = true;

                    fetch('{{ route("cla.horasmar.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            row: parseInt(personRow, 10),
                            col: parseInt(checkbox.dataset.col, 10),
                            value: original,
                        }),
                    })
                        .then(function (res) {
                            if (!res.ok) throw new Error('Falhou');
                            checkbox.disabled = false;
                            atualizarPainelPessoa(personRow, horas, original);
                        })
                        .catch(function () {
                            checkbox.checked = !original;
                            checkbox.disabled = false;
                            alert('Não foi possível guardar. Tenta outra vez.');
                        });
                });
            });

            function formatarHoras(valor) {
                return (Math.round(valor * 10) / 10).toString().replace('.', ',');
            }

            function atualizarPainelPessoa(personRow, horas, marcado) {
                const card = document.querySelector('.pessoa-card[data-person-row="' + personRow + '"]');
                if (!card) return;

                const valorHoras = card.querySelector('.valor-horas');
                const valorAtividades = card.querySelector('.valor-atividades');
                const barra = card.querySelector('.barra-horas');

                let novoTotal = parseFloat(valorHoras.dataset.raw) + (marcado ? horas : -horas);
                novoTotal = Math.max(0, novoTotal);

                let novasAtividades = parseInt(valorAtividades.textContent, 10) + (marcado ? 1 : -1);
                novasAtividades = Math.max(0, novasAtividades);

                valorHoras.dataset.raw = novoTotal;
                valorHoras.textContent = formatarHoras(novoTotal);
                valorAtividades.textContent = novasAtividades;

                if (novoTotal > maxHoras) {
                    maxHoras = novoTotal;
                }

                const novaLargura = maxHoras > 0 ? Math.round((novoTotal / maxHoras) * 100) : 0;
                barra.style.width = novaLargura + '%';
            }
        });
    </script>
</x-app-layout>
