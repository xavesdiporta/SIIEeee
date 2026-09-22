<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-8 px-6 sm:px-8 lg:px-10">

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FEF3C7] text-[#78350F] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#D97706]"></span>
                    Gestão · Alcateia
                </div>
                <h1 class="text-xl font-bold text-[#78350F]">Progresso dos Lobitos</h1>
                <p class="text-sm text-[#92400E] mt-1">{{ count($lobitos ?? []) }} lobitos registados</p>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-[#FEF3C7] border border-[#FDE68A] text-[#78350F] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-[24px] shadow-sm border border-[#FDE68A] p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-[#78350F] uppercase tracking-widest">Tabela de Progresso</h3>
                <button type="button" onclick="document.getElementById('form-novo-lobito').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 bg-[#78350F] hover:bg-[#5C2A0A] text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Adicionar Lobito
                </button>
            </div>

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

            <div class="overflow-auto -mx-2 max-h-[640px] border border-[#FDE68A] rounded-xl">
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
                        <tr class="hover:bg-[#FFFBEB] transition-colors">
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
            <div class="flex items-center justify-between mt-4">
                <p id="alteracoes-info" class="text-xs text-[#92400E]">Sem alterações por gravar.</p>
                <button type="button" id="btn-guardar-alteracoes" disabled
                        class="inline-flex items-center gap-2 bg-[#D97706] hover:bg-[#B45309] disabled:bg-[#FDE68A] disabled:cursor-not-allowed disabled:text-[#92400E] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Enviar para a base de dados
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
                    const row = checkbox.closest('tr');
                    const cel = checkbox.closest('.cel-objetivo');

                    const valorTotal = row.querySelector('.valor-total');
                    const partes = valorTotal.textContent.split('/');
                    let atual = parseInt(partes[0], 10) + (checkbox.checked ? 1 : -1);
                    valorTotal.textContent = atual + '/' + partes[1].trim();

                    if (checkbox.checked === original) {
                        pendentes.delete(chave);
                        cel.classList.remove('bg-[#FEF9C3]');
                    } else {
                        pendentes.set(chave, {
                            user_id: parseInt(userId, 10),
                            reference: ref,
                            value: checkbox.checked,
                        });
                        cel.classList.add('bg-[#FEF9C3]');
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
                            const checkbox = document.querySelector(
                                '.toggle-objetivo[data-user="' + mudanca.user_id + '"][data-ref="' + mudanca.reference + '"]'
                            );
                            checkbox.dataset.original = mudanca.value ? '1' : '0';
                            checkbox.closest('.cel-objetivo').classList.remove('bg-[#FEF9C3]');
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
