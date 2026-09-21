<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-8 px-6 sm:px-8 lg:px-10">

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#DCFCE7] text-[#14532D] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span>
                    Gestão · Exploradores
                </div>
                <h1 class="text-xl font-bold text-[#14532D]">Progresso dos Exploradores</h1>
                <p class="text-sm text-[#166534] mt-1">{{ count($exploradores ?? []) }} exploradores registados</p>            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-[#DCFCE7] border border-[#BBF7D0] text-[#14532D] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-[24px] shadow-sm border border-[#BBF7D0] p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-[#14532D] uppercase tracking-widest">Tabela de Progresso</h3>
                <button type="button" onclick="document.getElementById('form-novo-explorador').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 bg-[#14532D] hover:bg-[#0F3D22] text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Adicionar Explorador
                </button>
            </div>

            {{-- Formulário: por agora só o nome --}}
            <form id="form-novo-explorador" method="POST" action="{{ route('expedicao.exploradores-gestao.store-user') }}"
                  class="hidden flex flex-col sm:flex-row sm:items-end gap-3 mb-6 bg-[#F0FDF4] border border-[#BBF7D0] rounded-2xl p-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-bold text-[#166534] uppercase tracking-wider mb-1.5">Nome do Explorador</label>
                    <input type="text" name="nome" required
                           class="w-full rounded-xl border-[#BBF7D0] bg-white text-[#14532D] text-sm focus:border-[#16A34A] focus:ring-[#16A34A]">
                </div>
                <button type="submit" class="bg-[#16A34A] hover:bg-[#15803D] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Adicionar
                </button>
            </form>

            <div class="overflow-auto -mx-2 max-h-[640px] border border-[#BBF7D0] rounded-xl">
                <table class="border-collapse text-sm min-w-full">
                    <thead>
                    <tr>
                        <th rowspan="2" class="sticky top-0 left-0 z-30 bg-[#F0FDF4] border-b border-r border-[#BBF7D0] px-3 py-2 text-left text-xs font-bold text-[#166534] uppercase whitespace-nowrap min-w-[160px]">Nome</th>
                        <th rowspan="2" class="sticky top-0 z-20 bg-[#F0FDF4] border-b border-r-2 border-[#BBF7D0] px-2 py-2 text-center text-xs font-bold text-[#166534] uppercase whitespace-nowrap">Total</th>
                        @foreach($categorias as $cat)
                            <th colspan="{{ count($cat['refs']) }}"
                                class="sticky top-0 z-10 border-b border-r-2 border-[#BBF7D0] px-2 py-1.5 text-center text-[10px] font-bold uppercase tracking-wide"
                                style="background-color: {{ $cat['color'] }}1a; color: {{ $cat['color'] }};">
                                {{ $cat['name'] }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($categorias as $cat)
                            @foreach($cat['refs'] as $ref)
                                <th class="sticky z-10 bg-[#FAFEFB] border-b border-r border-[#E4F4E8] px-1 py-1.5 text-center text-[10px] font-semibold text-[#15803D] w-9" style="top: 33px;">
                                    {{ $ref }}
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($exploradores as $explorador)
                        @php
                            $refsDoUser = $matriz[$explorador->id] ?? [];
                            $totalUser = count($refsDoUser);
                        @endphp
                        <tr class="hover:bg-[#F0FDF4] transition-colors">
                            <td class="sticky left-0 z-10 bg-white border-b border-r border-[#BBF7D0] px-3 py-2 text-[#14532D] font-medium whitespace-nowrap">
                                {{ $explorador->name }}
                            </td>
                            <td class="border-b border-r-2 border-[#BBF7D0] px-2 py-2 text-center font-semibold text-[#14532D] whitespace-nowrap valor-total">
                                {{ $totalUser }}/{{ $totalRefsAll }}
                            </td>
                            @foreach($categorias as $cat)
                                @foreach($cat['refs'] as $ref)
                                    @php $marcado = in_array($ref, $refsDoUser, true); @endphp
                                    <td class="border-b border-r border-[#E4F4E8] text-center cel-objetivo">
                                        <input type="checkbox"
                                               class="toggle-objetivo w-4 h-4 cursor-pointer"
                                               style="accent-color: {{ $cat['color'] }};"
                                               data-user="{{ $explorador->id }}"
                                               data-ref="{{ $ref }}"
                                               data-original="{{ $marcado ? '1' : '0' }}"
                                            {{ $marcado ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + $totalRefsAll }}" class="py-10 text-center text-sm text-[#166534]">
                                Ainda não há exploradores registados. Usa o botão "Adicionar Explorador" acima.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between mt-4">
                <p id="alteracoes-info" class="text-xs text-[#166534]">Sem alterações por gravar.</p>
                <button type="button" id="btn-guardar-alteracoes" disabled
                        class="inline-flex items-center gap-2 bg-[#16A34A] hover:bg-[#15803D] disabled:bg-[#BBF7D0] disabled:cursor-not-allowed disabled:text-[#166534] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
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

            // Guarda as alterações pendentes por "user-ref" -> {user_id, reference, value}
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

                    // Atualiza o total da linha localmente (só visual, ainda não gravado)
                    const valorTotal = row.querySelector('.valor-total');
                    const partes = valorTotal.textContent.split('/');
                    let atual = parseInt(partes[0], 10) + (checkbox.checked ? 1 : -1);
                    valorTotal.textContent = atual + '/' + partes[1].trim();

                    if (checkbox.checked === original) {
                        // Voltou ao estado original: já não é uma alteração pendente
                        pendentes.delete(chave);
                        cel.classList.remove('bg-[#FEF9C3]');
                    } else {
                        pendentes.set(chave, {
                            user_id: parseInt(userId, 10),
                            reference: ref,
                            value: checkbox.checked,
                        });
                        cel.classList.add('bg-[#FEF9C3]'); // destaque amarelo = por gravar
                    }

                    atualizarBarra();
                });
            });

            btnGuardar.addEventListener('click', function () {
                if (pendentes.size === 0) return;

                const changes = Array.from(pendentes.values());
                btnGuardar.disabled = true;
                btnGuardar.textContent = 'A gravar...';

                fetch('{{ route("expedicao.exploradores-gestao.toggle-bulk") }}', {
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
                        // Sucesso: marca tudo como gravado
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
