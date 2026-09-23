<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-4 sm:py-8 px-3 sm:px-8 lg:px-10">

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FEF3C7] text-[#78350F] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#D97706]"></span>
                    Gestão · Alcateia
                </div>
                <h1 class="text-xl font-bold text-[#78350F]">Progresso dos Lobitos</h1>
                <p class="text-xs sm:text-sm text-[#92400E] mt-0.5">{{ count($lobitos ?? []) }} lobitos registados</p>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-[#FEF3C7] border border-[#FDE68A] text-[#78350F] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm border border-[#FDE68A] p-4 sm:p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs sm:text-sm font-bold text-[#78350F] uppercase tracking-widest">Tabela de Progresso</h3>
                <button type="button" onclick="document.getElementById('form-novo-lobito').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 bg-[#78350F] hover:bg-[#5C2A0A] text-white text-xs font-semibold px-3 py-2 rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Adicionar Lobito
                </button>
            </div>

            {{-- FORMULÁRIO --}}
            <form id="form-novo-lobito" method="POST" action="{{ route('alcateia.lobitos-gestao.store-user') }}"
                  class="hidden flex flex-col sm:flex-row sm:items-end gap-3 mb-6 bg-[#FFFBEB] border border-[#FDE68A] rounded-2xl p-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-bold text-[#92400E] uppercase tracking-wider mb-1.5">Nome do Lobito</label>
                    <input type="text" name="nome" required
                           class="w-full rounded-xl border-[#FDE68A] bg-white text-[#78350F] text-sm focus:border-[#D97706] focus:ring-[#D97706]">
                </div>
                <button type="submit" class="bg-[#D97706] hover:bg-[#B45309] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Adicionar
                </button>
            </form>

            <!-- VISTA MOBILE EM CARTÕES -->
            <div class="block md:hidden space-y-3 mb-6">
                @forelse($lobitos as $lobito)
                    @php
                        $refsDoUser = $matriz[$lobito->id] ?? [];
                        $totalUser = count($refsDoUser);
                        $initials = collect(explode(' ', trim($lobito->name)))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                    @endphp
                    <div x-data="{ open: false }" class="user-card border border-[#FDE68A] bg-[#FFFBEB] rounded-2xl p-3.5">
                        <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#78350F] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper($initials) ?: '?' }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#78350F]">{{ $lobito->name }}</h4>
                                    <p class="text-xs text-[#92400E] font-medium mt-0.5">Progresso: <span class="valor-total font-bold text-[#D97706]">{{ $totalUser }}/{{ $totalRefsAll }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-[10px] uppercase font-bold text-[#92400E]" x-text="open ? 'Fechar' : 'Etapas'"></span>
                                <div class="w-6 h-6 rounded-full bg-white border border-[#FDE68A] flex items-center justify-center text-[#92400E] transition-transform duration-200" :class="{ 'rotate-180': open }">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div x-show="open" x-cloak class="mt-4 pt-3 border-t border-[#FDE68A] space-y-4">
                            @foreach($categorias as $cat)
                                <div class="bg-white rounded-xl p-3 border border-[#FDE68A]">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md" style="background-color: {{ $cat['color'] }}1a; color: {{ $cat['color'] }};">
                                            {{ $cat['name'] }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                        @foreach($cat['refs'] as $ref)
                                            @php $marcado = in_array($ref, $refsDoUser, true); @endphp
                                            <label class="cel-objetivo flex items-center justify-center gap-1.5 p-2 rounded-lg border border-gray-100 bg-[#FFFBEB]/50 cursor-pointer active:scale-95 transition-all">
                                                <input type="checkbox"
                                                       class="toggle-objetivo w-4 h-4 cursor-pointer rounded"
                                                       style="accent-color: {{ $cat['color'] }};"
                                                       data-user="{{ $lobito->id }}"
                                                       data-ref="{{ $ref }}"
                                                       data-original="{{ $marcado ? '1' : '0' }}"
                                                    {{ $marcado ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-[#78350F]">{{ $ref }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#92400E] text-center py-6">Ainda não há lobitos registados.</p>
                @endforelse
            </div>

            <!-- VISTA EM TABELA MATRIZ PARA DESKTOP -->
            <div class="hidden md:block overflow-auto -mx-2 max-h-[640px] border border-[#FDE68A] rounded-xl">
                <table class="border-collapse text-sm min-w-full">
                    <thead>
                    <tr>
                        <th rowspan="2" class="sticky top-0 left-0 z-30 bg-[#FFFBEB] border-b border-r border-[#FDE68A] px-3 py-2 text-left text-xs font-bold text-[#92400E] uppercase whitespace-nowrap min-w-[160px]">Nome</th>
                        <th rowspan="2" class="sticky top-0 z-20 bg-[#FFFBEB] border-b border-r-2 border-[#FDE68A] px-2 py-2 text-center text-xs font-bold text-[#92400E] uppercase whitespace-nowrap">Total</th>
                        @foreach($categorias as $cat)
                            <th colspan="{{ count($cat['refs']) }}"
                                class="sticky top-0 z-10 border-b border-r-2 border-[#FDE68A] px-2 py-1.5 text-center text-[10px] font-bold uppercase tracking-wide"
                                style="background-color: {{ $cat['color'] }}1a; color: {{ $cat['color'] }};">
                                {{ $cat['name'] }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($categorias as $cat)
                            @foreach($cat['refs'] as $ref)
                                <th class="sticky z-10 bg-[#FFFDF5] border-b border-r border-[#FEF3C7] px-1 py-1.5 text-center text-[10px] font-semibold text-[#B45309] w-9" style="top: 33px;">
                                    {{ $ref }}
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lobitos as $lobito)
                        @php
                            $refsDoUser = $matriz[$lobito->id] ?? [];
                            $totalUser = count($refsDoUser);
                        @endphp
                        <tr class="user-card hover:bg-[#FFFBEB] transition-colors">
                            <td class="sticky left-0 z-10 bg-white border-b border-r border-[#FDE68A] px-3 py-2 text-[#78350F] font-medium whitespace-nowrap">
                                {{ $lobito->name }}
                            </td>
                            <td class="border-b border-r-2 border-[#FDE68A] px-2 py-2 text-center font-semibold text-[#78350F] whitespace-nowrap valor-total">
                                {{ $totalUser }}/{{ $totalRefsAll }}
                            </td>
                            @foreach($categorias as $cat)
                                @foreach($cat['refs'] as $ref)
                                    @php $marcado = in_array($ref, $refsDoUser, true); @endphp
                                    <td class="border-b border-r border-[#FEF3C7] text-center cel-objetivo">
                                        <input type="checkbox"
                                               class="toggle-objetivo w-4 h-4 cursor-pointer"
                                               style="accent-color: {{ $cat['color'] }};"
                                               data-user="{{ $lobito->id }}"
                                               data-ref="{{ $ref }}"
                                               data-original="{{ $marcado ? '1' : '0' }}"
                                            {{ $marcado ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + $totalRefsAll }}" class="py-10 text-center text-sm text-[#92400E]">
                                Ainda não há lobitos registados. Usa o botão "Adicionar Lobito" acima.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- BARRA DE AÇÃO FIXA --}}
            <div class="fixed md:static bottom-4 left-4 right-4 z-40 bg-white md:bg-transparent p-3 md:p-0 rounded-2xl md:rounded-none shadow-lg md:shadow-none border border-[#FDE68A] md:border-none flex items-center justify-between gap-3 mt-4">
                <p id="alteracoes-info" class="text-xs text-[#92400E] font-medium">Sem alterações por gravar.</p>
                <button type="button" id="btn-guardar-alteracoes" disabled
                        class="inline-flex items-center gap-2 bg-[#D97706] hover:bg-[#B45309] disabled:bg-[#FDE68A] disabled:cursor-not-allowed disabled:text-[#92400E] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm">
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

                fetch('{{ route("alcateia.lobitos-gestao.toggle-bulk") }}', {
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
