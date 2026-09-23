<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-4 sm:py-8 px-3 sm:px-8 lg:px-10">

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#DBEAFE] text-[#1E3A5F] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#2563EB]"></span>
                    Gestão · Comunidade
                </div>
                <h1 class="text-xl font-bold text-[#1E3A5F]">Progresso dos Pioneiros</h1>
                <p class="text-xs sm:text-sm text-[#1D4ED8] mt-0.5">{{ count($pioneiros ?? []) }} pioneiros registados</p>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-[#DBEAFE] border border-[#BFDBFE] text-[#1E3A5F] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#BFDBFE] p-4 sm:p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs sm:text-sm font-bold text-[#1E3A5F] uppercase tracking-widest">Tabela de Progresso</h3>
                <button type="button" onclick="document.getElementById('form-novo-pioneiro').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 bg-[#1E3A5F] hover:bg-[#16324D] text-white text-xs font-semibold px-3 py-2 rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Adicionar Pioneiro
                </button>
            </div>

            {{-- FORMULÁRIO --}}
            <form id="form-novo-pioneiro" method="POST" action="{{ route('comunidade.pioneiros-gestao.store-user') }}"
                  class="hidden flex flex-col sm:flex-row sm:items-end gap-3 mb-6 bg-[#EFF6FF] border border-[#BFDBFE] rounded-2xl p-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-bold text-[#1D4ED8] uppercase tracking-wider mb-1.5">Nome do Pioneiro</label>
                    <input type="text" name="nome" required
                           class="w-full rounded-xl border-[#BFDBFE] bg-white text-[#1E3A5F] text-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                </div>
                <button type="submit" class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Adicionar
                </button>
            </form>

            <!-- VISTA MOBILE EM CARTÕES (Exibida apenas abaixo de md) -->
            <div class="block md:hidden space-y-3 mb-6">
                @forelse($pioneiros as $pioneiro)
                    @php
                        $refsDoUser = $matriz[$pioneiro->id] ?? [];
                        $totalUser = count($refsDoUser);
                        $initials = collect(explode(' ', trim($pioneiro->name)))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                    @endphp
                    <div x-data="{ open: false }" class="user-card border border-[#BFDBFE] bg-[#EFF6FF] rounded-2xl p-3.5">
                        <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#1E3A5F] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper($initials) ?: '?' }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#1E3A5F]">{{ $pioneiro->name }}</h4>
                                    <p class="text-xs text-[#1D4ED8] font-medium mt-0.5">Progresso: <span class="valor-total font-bold text-[#2563EB]">{{ $totalUser }}/{{ $totalRefsAll }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-[10px] uppercase font-bold text-[#1D4ED8]" x-text="open ? 'Fechar' : 'Etapas'"></span>
                                <div class="w-6 h-6 rounded-full bg-white border border-[#BFDBFE] flex items-center justify-center text-[#1D4ED8] transition-transform duration-200" :class="{ 'rotate-180': open }">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div x-show="open" x-cloak class="mt-4 pt-3 border-t border-[#BFDBFE] space-y-4">
                            @foreach($categorias as $cat)
                                <div class="bg-white rounded-xl p-3 border border-[#BFDBFE]">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md" style="background-color: {{ $cat['color'] }}1a; color: {{ $cat['color'] }};">
                                            {{ $cat['name'] }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                        @foreach($cat['refs'] as $ref)
                                            @php $marcado = in_array($ref, $refsDoUser, true); @endphp
                                            <label class="cel-objetivo flex items-center justify-center gap-1.5 p-2 rounded-lg border border-gray-100 bg-[#EFF6FF]/50 cursor-pointer active:scale-95 transition-all">
                                                <input type="checkbox"
                                                       class="toggle-objetivo w-4 h-4 cursor-pointer rounded"
                                                       style="accent-color: {{ $cat['color'] }};"
                                                       data-user="{{ $pioneiro->id }}"
                                                       data-ref="{{ $ref }}"
                                                       data-original="{{ $marcado ? '1' : '0' }}"
                                                    {{ $marcado ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-[#1E3A5F]">{{ $ref }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#1D4ED8] text-center py-6">Ainda não há pioneiros registados.</p>
                @endforelse
            </div>

            <!-- VISTA EM TABELA MATRIZ PARA DESKTOP (Exibida a partir de md) -->
            <div class="hidden md:block overflow-auto -mx-2 max-h-[640px] border border-[#BFDBFE] rounded-xl">
                <table class="border-collapse text-sm min-w-full">
                    <thead>
                    <tr>
                        <th rowspan="2" class="sticky top-0 left-0 z-30 bg-[#EFF6FF] border-b border-r border-[#BFDBFE] px-3 py-2 text-left text-xs font-bold text-[#1D4ED8] uppercase whitespace-nowrap min-w-[160px]">Nome</th>
                        <th rowspan="2" class="sticky top-0 z-20 bg-[#EFF6FF] border-b border-r-2 border-[#BFDBFE] px-2 py-2 text-center text-xs font-bold text-[#1D4ED8] uppercase whitespace-nowrap">Total</th>
                        @foreach($categorias as $cat)
                            <th colspan="{{ count($cat['refs']) }}"
                                class="sticky top-0 z-10 border-b border-r-2 border-[#BFDBFE] px-2 py-1.5 text-center text-[10px] font-bold uppercase tracking-wide"
                                style="background-color: {{ $cat['color'] }}1a; color: {{ $cat['color'] }};">
                                {{ $cat['name'] }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($categorias as $cat)
                            @foreach($cat['refs'] as $ref)
                                <th class="sticky z-10 bg-[#F8FAFF] border-b border-r border-[#DBEAFE] px-1 py-1.5 text-center text-[10px] font-semibold text-[#1D4ED8] w-9" style="top: 33px;">
                                    {{ $ref }}
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($pioneiros as $pioneiro)
                        @php
                            $refsDoUser = $matriz[$pioneiro->id] ?? [];
                            $totalUser = count($refsDoUser);
                        @endphp
                        <tr class="user-card hover:bg-[#EFF6FF] transition-colors">
                            <td class="sticky left-0 z-10 bg-white border-b border-r border-[#BFDBFE] px-3 py-2 text-[#1E3A5F] font-medium whitespace-nowrap">
                                {{ $pioneiro->name }}
                            </td>
                            <td class="border-b border-r-2 border-[#BFDBFE] px-2 py-2 text-center font-semibold text-[#1E3A5F] whitespace-nowrap valor-total">
                                {{ $totalUser }}/{{ $totalRefsAll }}
                            </td>
                            @foreach($categorias as $cat)
                                @foreach($cat['refs'] as $ref)
                                    @php $marcado = in_array($ref, $refsDoUser, true); @endphp
                                    <td class="border-b border-r border-[#DBEAFE] text-center cel-objetivo">
                                        <input type="checkbox"
                                               class="toggle-objetivo w-4 h-4 cursor-pointer"
                                               style="accent-color: {{ $cat['color'] }};"
                                               data-user="{{ $pioneiro->id }}"
                                               data-ref="{{ $ref }}"
                                               data-original="{{ $marcado ? '1' : '0' }}"
                                            {{ $marcado ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + $totalRefsAll }}" class="py-10 text-center text-sm text-[#1D4ED8]">
                                Ainda não há pioneiros registados. Usa o botão "Adicionar Pioneiro" acima.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- BARRA DE AÇÃO (FIXA AO FUNDO EM MOBILE QUANDO HÁ ALTERAÇÕES) --}}
            <div class="fixed md:static bottom-4 left-4 right-4 z-40 bg-white md:bg-transparent p-3 md:p-0 rounded-2xl md:rounded-none shadow-lg md:shadow-none border border-[#BFDBFE] md:border-none flex items-center justify-between gap-3 mt-4">
                <p id="alteracoes-info" class="text-xs text-[#1D4ED8] font-medium">Sem alterações por gravar.</p>
                <button type="button" id="btn-guardar-alteracoes" disabled
                        class="inline-flex items-center gap-2 bg-[#2563EB] hover:bg-[#1D4ED8] disabled:bg-[#BFDBFE] disabled:cursor-not-allowed disabled:text-[#1D4ED8] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Enviar para a base de dados</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const btnGuardar = document.getElementById('btn-guardar-alteracoes');
            const infoAlteracoes = document.getElementById('alteracoes-info');
            const pendentes = new Map();

            function atualizarBarra() {
                const n = pendentes.size;
                btnGuardar.disabled = n === 0;
                infoAlteracoes.textContent = n === 0
                    ? 'Sem alterações por gravar.'
                    : n + ' etapa(s) por gravar.';
            }

            document.querySelectorAll('.toggle-objetivo').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const userId = checkbox.dataset.user;
                    const ref = checkbox.dataset.ref;
                    const original = checkbox.dataset.original === '1';
                    const chave = userId + '-' + ref;

                    // Sincroniza todas as instâncias do mesmo checkbox (mobile e desktop)
                    document.querySelectorAll('.toggle-objetivo[data-user="' + userId + '"][data-ref="' + ref + '"]').forEach(function (el) {
                        el.checked = checkbox.checked;
                        const celEl = el.closest('.cel-objetivo');
                        if (celEl) {
                            if (checkbox.checked === original) {
                                celEl.classList.remove('bg-[#FEF9C3]');
                            } else {
                                celEl.classList.add('bg-[#FEF9C3]');
                            }
                        }
                    });

                    // Atualiza os contadores locais do utilizador
                    document.querySelectorAll('.user-card').forEach(function (card) {
                        const userCheck = card.querySelector('.toggle-objetivo[data-user="' + userId + '"]');
                        if (userCheck) {
                            const valorTotal = card.querySelector('.valor-total');
                            if (valorTotal) {
                                const partes = valorTotal.textContent.split('/');
                                let atual = parseInt(partes[0], 10) + (checkbox.checked ? 1 : -1);
                                valorTotal.textContent = atual + '/' + partes[1].trim();
                            }
                        }
                    });

                    if (checkbox.checked === original) {
                        pendentes.delete(chave);
                    } else {
                        pendentes.set(chave, {
                            user_id: parseInt(userId, 10),
                            reference: ref,
                            value: checkbox.checked,
                        });
                    }

                    atualizarBarra();
                });
            });

            btnGuardar.addEventListener('click', function () {
                if (pendentes.size === 0) return;

                const changes = Array.from(pendentes.values());
                btnGuardar.disabled = true;
                btnGuardar.textContent = 'A gravar...';

                fetch('{{ route("comunidade.pioneiros-gestao.toggle-bulk") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ changes: changes }),
                })
                    .then(function (res) {
                        if (!res.ok) throw new Error('Falhou');
                        return res.json();
                    })
                    .then(function () {
                        pendentes.forEach(function (mudanca) {
                            document.querySelectorAll('.toggle-objetivo[data-user="' + mudanca.user_id + '"][data-ref="' + mudanca.reference + '"]').forEach(function (checkbox) {
                                checkbox.dataset.original = mudanca.value ? '1' : '0';
                                const cel = checkbox.closest('.cel-objetivo');
                                if (cel) cel.classList.remove('bg-[#FEF9C3]');
                            });
                        });
                        pendentes.clear();
                        atualizarBarra();
                        btnGuardar.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Enviar para a base de dados';
                    })
                    .catch(function () {
                        alert('Não foi possível gravar as alterações. Tenta outra vez.');
                        btnGuardar.disabled = false;
                        btnGuardar.textContent = 'Enviar para a base de dados';
                    });
            });
        });
    </script>
</x-app-layout>
