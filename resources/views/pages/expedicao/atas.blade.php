<x-app-layout>
    <div class="max-w-[100rem] mx-auto py-4 sm:py-8 px-3 sm:px-8 lg:px-10">

        {{-- CABEÇALHO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#DCFCE7] text-[#14532D] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span>
                    Gestão · Expedição
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#14532D]">Atas da Expedição</h1>
                <p class="text-xs sm:text-sm text-[#166534] mt-0.5">Registo e arquivo de atas de reuniões e conselhos</p>
            </div>

            <button type="button" onclick="document.getElementById('form-nova-ata').classList.toggle('hidden')"
                    class="inline-flex items-center gap-2 bg-[#14532D] hover:bg-[#0F3D22] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm self-start sm:self-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nova Ata</span>
            </button>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-[#DCFCE7] border border-[#BBF7D0] text-[#14532D] text-sm rounded-xl px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        {{-- FORMULÁRIO DE NOVA ATA --}}
        <div id="form-nova-ata" class="hidden mb-6 bg-white border border-[#BBF7D0] rounded-[24px] p-4 sm:p-6 shadow-sm">
            <h3 class="text-sm font-bold text-[#14532D] uppercase tracking-wider mb-4">Adicionar Nova Ata</h3>

            <form method="POST" action="{{ route('expedicao.atas.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#166534] uppercase tracking-wider mb-1.5">Data da Reunião / Conselho</label>
                        <input type="date" name="data_ata" required
                               class="w-full rounded-xl border-[#BBF7D0] bg-[#F0FDF4] text-[#14532D] text-sm focus:border-[#16A34A] focus:ring-[#16A34A]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#166534] uppercase tracking-wider mb-1.5">Nome / Título da Ata</label>
                        <input type="text" name="nome" placeholder="Ex: Conselho de Guias #4" required
                               class="w-full rounded-xl border-[#BBF7D0] bg-[#F0FDF4] text-[#14532D] text-sm focus:border-[#16A34A] focus:ring-[#16A34A]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#166534] uppercase tracking-wider mb-1.5">Ficheiro (PDF, DOCX, Imagem)</label>
                    <input type="file" name="ficheiro" required
                           class="w-full text-xs text-[#166534] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#DCFCE7] file:text-[#14532D] hover:file:bg-[#BBF7D0] cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#166534] uppercase tracking-wider mb-1.5">Pontos Chave / Descrição Breve</label>
                    <textarea name="descricao" rows="3" placeholder="Resumo dos pontos principais tratados..."
                              class="w-full rounded-xl border-[#BBF7D0] bg-[#F0FDF4] text-[#14532D] text-sm focus:border-[#16A34A] focus:ring-[#16A34A]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('form-nova-ata').classList.add('hidden')"
                            class="px-4 py-2.5 text-xs font-bold text-[#166534] hover:bg-[#F0FDF4] rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-[#16A34A] hover:bg-[#15803D] text-white text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                        Guardar no Google Drive
                    </button>
                </div>
            </form>
        </div>

        {{-- LISTA DE ATAS EXISTENTES --}}
        <div class="bg-white rounded-[24px] shadow-sm border border-[#BBF7D0] p-4 sm:p-6">
            <h3 class="text-xs sm:text-sm font-bold text-[#14532D] uppercase tracking-widest mb-4">Histórico de Atas</h3>

            <div class="space-y-3">
                @forelse($atas ?? [] as $ata)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-[#F0FDF4] border border-[#BBF7D0] rounded-2xl hover:bg-[#E4F4E8] transition-colors">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-[#14532D] bg-white px-2 py-0.5 rounded-md border border-[#BBF7D0]">
                                    {{ \Carbon\Carbon::parse($ata->data_ata)->format('d/m/Y') }}
                                </span>
                                <h4 class="text-sm font-bold text-[#14532D]">{{ $ata->nome }}</h4>
                            </div>
                            @if($ata->descricao)
                                <p class="text-xs text-[#166534] mt-1">{{ $ata->descricao }}</p>
                            @endif
                        </div>

                        @if($ata->drive_link)
                            <a href="{{ $ata->drive_link }}" target="_blank"
                               class="inline-flex items-center gap-1.5 bg-white hover:bg-[#DCFCE7] text-[#14532D] text-xs font-bold px-3 py-2 rounded-xl border border-[#BBF7D0] transition-colors shrink-0 self-start sm:self-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span>Ver no Drive</span>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-[#166534] text-center py-8">Nenhuma ata registada até ao momento.</p>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
