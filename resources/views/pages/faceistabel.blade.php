<x-app-layout>
    <div class="py-6 sm:py-12 px-3 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        <livewire:content-modal />

        @php
            $categoriaAtiva = (int) ($categoriaAtiva ?? (Auth::user()?->categoria_ativa ?? 1));

            $approvedRefs = $completedRefs ?? \App\Models\ProgressNote::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->pluck('reference')
                ->toArray();

            $refCategorias = $refCategorias ?? [
                'F1' => 1, 'F2' => 2, 'F3' => 2, 'F4' => 3, 'F5' => 3, 'F6' => 3,
                'A1' => 1, 'A2' => 1, 'A3' => 1, 'A4' => 2, 'A5' => 3, 'A6' => 3,
                'C1' => 1, 'C2' => 1, 'C3' => 1, 'C4' => 2, 'C5' => 2, 'C6' => 2, 'C7' => 3, 'C8' => 3,
                'E1' => 1, 'E2' => 1, 'E3' => 1, 'E4' => 2, 'E5' => 2, 'E6' => 2, 'E7' => 3, 'E8' => 3,
                'I1' => 1, 'I2' => 1, 'I3' => 1, 'I4' => 2, 'I5' => 2, 'I6' => 3, 'I7' => 3,
                'S1' => 1, 'S2' => 1, 'S3' => 1, 'S4' => 2, 'S5' => 2, 'S6' => 2, 'S7' => 3,
            ];

            $dimensions = [
                [
                    'category' => 'Físico',
                    'initial' => 'F',
                    'rest' => 'ísico',
                    'color' => '#16a34a',
                    'subcategories' => [
                        [
                            'title' => 'Desempenho',
                            'items' => [
                                ['ref' => 'F1', 'text' => 'F1. Praticar actividade física que promova o desenvolvimento e manutenção da agilidade, flexibilidade e destreza de forma adequada à sua idade, capacidade e limitações.'],
                            ]
                        ],
                        [
                            'title' => 'Auto-conhecimento',
                            'items' => [
                                ['ref' => 'F2', 'text' => 'F2. Conhecer e aceitar o desenvolvimento e amadurecimento do seu corpo com naturalidade'],
                                ['ref' => 'F3', 'text' => 'F3. Conhecer as características fisiológicas do corpo masculino e feminino e a sua relação com o comportamento e necessidades individuais'],
                            ]
                        ],
                        [
                            'title' => 'Bem-estar Físico',
                            'items' => [
                                ['ref' => 'F4', 'text' => 'F4. Cultivar um estilo de vida saudável e equilibrado – alimentação, actividade física e repouso –, adaptado a cada fase do seu desenvolvimento'],
                                ['ref' => 'F5', 'text' => 'F5. Cuidar e valorizar o seu corpo de acordo com os padrões de saúde, revelando aprumo'],
                                ['ref' => 'F6', 'text' => 'F6. Identificar e evitar, na vida quotidiana, os comportamentos de risco relacionados com a segurança física e consumo de substância'],
                            ]
                        ]
                    ]
                ],
                [
                    'category' => 'Afectivo',
                    'initial' => 'A',
                    'rest' => 'fectivo',
                    'color' => '#dc2626',
                    'subcategories' => [
                        [
                            'title' => 'Relacionamento e Sensibilidade',
                            'items' => [
                                ['ref' => 'A1', 'text' => 'A1. Valorizar e demonstrar sensibilidade nas suas relações afectivas, de modo consequente com a opção de vida assumida'],
                                ['ref' => 'A2', 'text' => 'A2. Respeitar a existência de várias sensibilidades estéticas e artísticas, formando a sua opinião com sentido crítico'],
                                ['ref' => 'A3', 'text' => 'A3. Assumir a própria sexualidade aceitando a complementaridade Homem / Mulher e vivê-la como expressão responsável de amor'],
                            ]
                        ],
                        [
                            'title' => 'Equilíbrio emocional',
                            'items' => [
                                ['ref' => 'A4', 'text' => 'A4. Ser capaz de identificar, compreender e expressar as suas emoções, tendo em conta o contexto e os sentimentos dos outros'],
                            ]
                        ],
                        [
                            'title' => 'Auto-estima',
                            'items' => [
                                ['ref' => 'A5', 'text' => 'A5. Reconhecer e aceitar as características da sua personalidade, mantendo uma atitude de aperfeiçoamento constante'],
                                ['ref' => 'A6', 'text' => 'A6. Valorizar as próprias capacidades, superando limitações e adoptando uma atitude positiva perante a vida'],
                            ]
                        ]
                    ]
                ],
                [
                    'category' => 'Carácter',
                    'initial' => 'C',
                    'rest' => 'arácter',
                    'color' => '#2563eb',
                    'subcategories' => [
                        [
                            'title' => 'Autonomia',
                            'items' => [
                                ['ref' => 'C1', 'text' => 'C1. Possuir e desenvolver um quadro de valores que são fruto de uma opção consciente'],
                                ['ref' => 'C2', 'text' => 'C2. Ser capaz de formular e construir as suas próprias opções, assumindo-as com clareza'],
                                ['ref' => 'C3', 'text' => 'C3. Mostrar-se responsável pelo seu desenvolvimento, colocando a si próprio objectivos de progressão pessoal'],
                            ]
                        ],
                        [
                            'title' => 'Responsabilidade',
                            'items' => [
                                ['ref' => 'C4', 'text' => 'C4. Demonstrar empenho e vontade de agir, assumindo as suas responsabilidades em todos os projectos que enceta, estabelecendo prioridades e respeitando-as'],
                                ['ref' => 'C5', 'text' => 'C5. Demonstrar perseverança nos momentos de dificuldade, procurando ultrapassá-los com optimismo'],
                                ['ref' => 'C6', 'text' => 'C6. Ser consequente com as opções que toma, assumindo a responsabilidade pelos seus actos'],
                            ]
                        ],
                        [
                            'title' => 'Coerência',
                            'items' => [
                                ['ref' => 'C7', 'text' => 'C7. Ser consistente e convicto na defesa das suas ideias e valores'],
                                ['ref' => 'C8', 'text' => 'C8. Dar testemunho, agindo em coerência com o seu sistema de valores'],
                            ]
                        ]
                    ]
                ],
                [
                    'category' => 'Espiritual',
                    'initial' => 'E',
                    'rest' => 'spiritual',
                    'color' => '#9333ea',
                    'subcategories' => [
                        [
                            'title' => 'Descoberta',
                            'items' => [
                                ['ref' => 'E1', 'text' => 'E1 Conhecer e compreender o modo como Deus se deu a conhecer à humanidade, propondo-lhe um Projecto de Felicidade Plena (História da Salvação).'],
                                ['ref' => 'E2', 'text' => 'E2 Conhecer em profundidade a mensagem e a proposta de Jesus Cristo (Mistério da Encarnação e Mistério Pascal).'],
                                ['ref' => 'E3', 'text' => 'E3 Reconhecer que a pertença à Igreja é um sinal de Deus no mundo de hoje (Igreja Sacramento Universal de Salvação).'],
                            ]
                        ],
                        [
                            'title' => 'Aprofundamento',
                            'items' => [
                                ['ref' => 'E4', 'text' => 'E4 Aprofundar os hábitos de oração pessoal e assumir-se como membro activo da Igreja na celebração comunitária.'],
                                ['ref' => 'E5', 'text' => 'E5 Integrar na sua vida os valores do Evangelho, vivendo as propostas da Igreja.'],
                                ['ref' => 'E6', 'text' => 'E6 Conhecer as principais religiões distinguindo e valorizando a identidade da Igreja Católica.'],
                            ]
                        ],
                        [
                            'title' => 'Serviço',
                            'items' => [
                                ['ref' => 'E7', 'text' => 'E7 Testemunhar que a presença de Deus no mundo dignifica a vida humana e a natureza'],
                                ['ref' => 'E8', 'text' => 'E8 Viver o compromisso Cristão como missão no mundo em todas as dimensões (humanas, sociais, económicas, culturais e políticas).'],
                            ]
                        ]
                    ]
                ],
                [
                    'category' => 'Intelectual',
                    'initial' => 'I',
                    'rest' => 'ntelectual',
                    'color' => '#f97316',
                    'subcategories' => [
                        [
                            'title' => 'Procura do conhecimento',
                            'items' => [
                                ['ref' => 'I1', 'text' => 'I1. Procurar de forma activa e continuada novos saberes e vivências, como forma de contribuir para o seu crescimento pessoal'],
                                ['ref' => 'I2', 'text' => 'I2. Conhecer e utilizar formas adequadas de recolha e tratamento de informação e, dentro dessas, distinguir o essencial do acessório'],
                                ['ref' => 'I3', 'text' => 'I3. Definir o seu itinerário de formação preocupando-se em mantê-lo actualizado'],
                            ]
                        ],
                        [
                            'title' => 'Resolução de problemas',
                            'items' => [
                                ['ref' => 'I4', 'text' => 'I4. Adaptar-se e superar novas situações, avaliando-as à luz de experiências anteriores e conhecimentos adquiridos'],
                                ['ref' => 'I5', 'text' => 'I5. Analisar os problemas de forma crítica, sugerindo e aplicando estratégias de resolução'],
                            ]
                        ],
                        [
                            'title' => 'Criatividade e Expressão',
                            'items' => [
                                ['ref' => 'I6', 'text' => 'I6. Ser capaz de utilizar conhecimentos, percepções e intuições na criação de novas ideias e obras, mantendo um espírito aberto e inovador'],
                                ['ref' => 'I7', 'text' => 'I7 Expressar ideias e emoções de forma lógica e criativa, adaptada ao(s) destinatário(s) e utilizando os meios adequados.'],
                            ]
                        ]
                    ]
                ],
                [
                    'category' => 'Social',
                    'initial' => 'S',
                    'rest' => 'ocial',
                    'color' => '#eab308',
                    'subcategories' => [
                        [
                            'title' => 'Exercer ativamente Cidadania',
                            'items' => [
                                ['ref' => 'S1', 'text' => 'S1. Conhecer e exercer os seus direitos e deveres enquanto cidadão'],
                                ['ref' => 'S2', 'text' => 'S2. Participar activa e conscientemente nos vários espaços sociais onde se insere, intervindo de uma forma informada, respeitadora e construtiva'],
                                ['ref' => 'S3', 'text' => 'S3. Respeitar as regras democráticas e assumir como suas as decisões tomadas colectivamente'],
                            ]
                        ],
                        [
                            'title' => 'Solidariedade e tolerância',
                            'items' => [
                                ['ref' => 'S4', 'text' => 'S4. Assumir que é parte da sociedade onde se insere, agindo numa perspectiva de serviço libertador e de construção de futuro'],
                                ['ref' => 'S5', 'text' => 'S5. Usar de empatia na forma de comunicar com os outros, demonstrando tolerância e respeito perante outros pontos de vista'],
                            ]
                        ],
                        [
                            'title' => 'Interacções e cooperação',
                            'items' => [
                                ['ref' => 'S6', 'text' => 'S6. Mostrar capacidade de relacionamento e trabalho em equipa, contribuindo activamente para o sucesso do colectivo através do desempenho com competência do seu papel'],
                                ['ref' => 'S7', 'text' => 'S7. Assumir papéis de liderança, de forma equilibrada, tendo em conta as suas necessidades e as do grupo'],
                            ]
                        ]
                    ]
                ]
            ];
        @endphp

        <!-- Cabeçalho com Seletor de Categoria Ativa -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#3E2D1B]">Grelha de Objetivos do Sistema de Progresso</h1>
                <p class="text-xs sm:text-sm text-[#776246] mt-1">
                    Clica numa dimensão para ver as áreas. Apenas os objetivos da <strong>Categoria {{ $categoriaAtiva }}</strong> estão desbloqueados para progresso.
                </p>
            </div>

            {{-- Select para escolher a categoria a trabalhar --}}
            <div class="flex items-center gap-2.5 bg-white border border-[#E4D5C3] px-3.5 py-2 rounded-2xl shadow-xs self-start md:self-auto">
                <label for="select-categoria-faceis" class="text-xs font-bold text-[#776246] uppercase tracking-wider whitespace-nowrap">
                    Categoria a trabalhar:
                </label>
                <select id="select-categoria-faceis"
                        onchange="mudarCategoriaCla(this.value)"
                        class="text-xs font-bold text-[#3E2D1B] bg-[#FAF7F5] border border-[#E4D5C3] rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-[#776246] focus:border-[#776246] cursor-pointer">
                    <option value="1" {{ $categoriaAtiva === 1 ? 'selected' : '' }}>
                        Categoria 1 (1ª Etapa - Comunidade) {{ ($progressoEtapas['cat1_completa'] ?? false) ? '✓' : '' }}
                    </option>
                    <option value="2" {{ $categoriaAtiva === 2 ? 'selected' : '' }}>
                        Categoria 2 (2ª Etapa - Partida) {{ ($progressoEtapas['cat2_completa'] ?? false) ? '✓' : '' }}
                    </option>
                    <option value="3" {{ $categoriaAtiva === 3 ? 'selected' : '' }}>
                        Categoria 3 (3ª Etapa - Serviço) {{ ($progressoEtapas['cat3_completa'] ?? false) ? '✓' : '' }}
                    </option>
                </select>
                <span id="categoria-cla-loading" class="hidden text-xs text-[#DC2626] font-semibold animate-pulse">A guardar...</span>
            </div>
        </div>

        <!-- Lista de Acordeões por Dimensão -->
        <div class="space-y-4">
            @foreach($dimensions as $dim)
                @php
                    $allItems = collect($dim['subcategories'])->flatMap(fn($sub) => $sub['items']);
                    $totalCat = $allItems->count();
                    $completedCat = $allItems->filter(fn($item) => in_array($item['ref'], $approvedRefs))->count();
                @endphp

                <div x-data="{ open: false }"
                     class="bg-white rounded-2xl sm:rounded-[24px] shadow-sm border border-[#E4D5C3] overflow-hidden transition-all duration-200">

                    <!-- Botão do Acordeão (Nível 1: Categoria) -->
                    <button @click="open = !open"
                            type="button"
                            class="w-full flex items-center justify-between p-4 sm:p-6 text-left focus:outline-none transition-colors hover:bg-[#FAF7F5]">
                        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                            <!-- Badge com a Inicial da Dimensão -->
                            <div class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl text-white shadow-md shrink-0"
                                 style="background-color: {{ $dim['color'] }};">
                                <span class="text-2xl sm:text-3xl font-black leading-none">{{ $dim['initial'] }}</span>
                            </div>

                            <div class="min-w-0">
                                <h2 class="text-base sm:text-xl font-bold text-[#3E2D1B] truncate">
                                    {{ $dim['initial'] }}{{ $dim['rest'] }}
                                </h2>
                                <span class="inline-block text-xs font-semibold text-[#B0977A] mt-0.5">
                                    {{ $completedCat }} de {{ $totalCat }} concluídos
                                </span>
                            </div>
                        </div>

                        <!-- Indicador de Expandir/Colapsar -->
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="hidden sm:inline-block text-xs font-bold uppercase tracking-wider text-[#776246]" x-text="open ? 'Ocultar' : 'Ver tudo'"></span>
                            <div class="w-8 h-8 rounded-full bg-[#FAF7F5] border border-[#E4D5C3] flex items-center justify-center text-[#776246] transition-transform duration-200"
                                 :class="{ 'rotate-180': open }">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- Conteúdo Expandível -->
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-[2000px]"
                         class="border-t border-[#E4D5C3] bg-[#FAF7F5] p-4 sm:p-6 space-y-6">

                        @foreach($dim['subcategories'] as $subCatIndex => $sub)
                            @php
                                $subCatNum = $subCatIndex + 1;
                                $isSubCatActive = ($subCatNum === (int) $categoriaAtiva);
                                $subAllDone = collect($sub['items'])->every(fn($it) => in_array($it['ref'], $approvedRefs));
                            @endphp
                            <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-5 border border-[#E4D5C3] shadow-2xs {{ !$isSubCatActive ? 'bg-gray-50/50' : '' }}">
                                <!-- Nível 2: Área de Desenvolvimento -->
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider flex items-center gap-2"
                                        style="color: {{ $dim['color'] }};">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $dim['color'] }};"></span>
                                        {{ $sub['title'] }}
                                    </h3>

                                    <div class="flex items-center gap-2">
                                        @if($subAllDone)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">
                                                Concluída ✓
                                            </span>
                                        @elseif($isSubCatActive)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Categoria Ativa ({{ $subCatNum }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500 border border-gray-200" title="Ativa a Categoria {{ $subCatNum }} no topo para desbloquear estes objetivos">
                                                <svg class="w-2.5 h-2.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                </svg>
                                                Categoria {{ $subCatNum }} (Bloqueada)
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Nível 3: Objetivos / Referências -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-3">
                                    @foreach($sub['items'] as $item)
                                        @php
                                            $isCompleted = in_array($item['ref'], $approvedRefs);
                                            $refCat = $refCategorias[$item['ref']] ?? $subCatNum;
                                            $isActive = ($refCat === (int) $categoriaAtiva);
                                        @endphp
                                        <div @if($isActive) @click="$dispatch('open-content-modal', { category: '{{ $dim['category'] }}', content: '{{ addslashes($item['text']) }}', reference: '{{ $item['ref'] }}', color: '{{ $dim['color'] }}' })" @endif
                                             title="{{ !$isActive && !$isCompleted ? 'Bloqueado: Pertence à Categoria ' . $refCat . '. Seleciona a Categoria ' . $refCat . ' no topo para trabalhar neste objetivo.' : ($isCompleted ? 'Objetivo já aprovado' : 'Clica para registar progresso') }}"
                                             class="p-3 rounded-xl border transition-all flex items-start gap-3 group relative {{ $isActive ? 'cursor-pointer border-[#E4D5C3] hover:border-gray-400 hover:bg-[#FAF7F5] ' . ($isCompleted ? 'bg-gray-50/70 border-gray-200' : 'bg-white shadow-2xs') : ($isCompleted ? 'bg-gray-50/70 border-gray-200 opacity-60 cursor-default' : 'bg-gray-100/70 border-dashed border-gray-300 opacity-40 cursor-not-allowed') }}">

                                            <span class="px-2 py-1 rounded-md text-xs font-bold text-white shrink-0 mt-0.5 shadow-xs"
                                                  style="background-color: {{ $isActive || $isCompleted ? $dim['color'] : '#9CA3AF' }};">
                                                {{ $item['ref'] }}
                                            </span>

                                            <p class="text-xs text-[#3E2D1B] leading-relaxed flex-1 {{ $isCompleted ? 'line-through decoration-2 opacity-50' : (!$isActive ? 'text-gray-500' : '') }}">
                                                {{ $item['text'] }}
                                            </p>

                                            @if($isCompleted)
                                                <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background-color: {{ $dim['color'] }};" title="Aprovado">
                                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @elseif(!$isActive)
                                                <div class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center shrink-0 text-gray-500" title="Bloqueado (Pertence à Categoria {{ $refCat }})">
                                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <script>
        function mudarCategoriaCla(cat) {
            const loading = document.getElementById('categoria-cla-loading');
            if (loading) loading.classList.remove('hidden');

            fetch('{{ route('cla.set-categoria') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    categoria: parseInt(cat)
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    window.location.reload();
                } else {
                    alert('Erro ao atualizar categoria');
                    if (loading) loading.classList.add('hidden');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro ao comunicar com o servidor');
                if (loading) loading.classList.add('hidden');
            });
        }
    </script>
</x-app-layout>
