<x-app-layout>
    <div class="py-12">

        <livewire:content-modal />

        @php
            $approvedRefs = \App\Models\ProgressNote::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->pluck('reference')
                ->toArray();
        @endphp

        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 min-h-[85vh]">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[30px] h-full flex flex-col">

                <table class="min-w-full h-full border-collapse">
                    <thead>
                    <tr>
                        <th class="text-left w-1/5"></th>
                        <th class="text-left w-1/5"></th>
                        <th class="text-left w-3/5"></th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">

                    <!-- Linha 1: Verde - Físico -->
                    <tr style="height: 16.66%; min-height: 120px;" class="border-b-8 border-[#FAF7F5]" x-data="{ category: 'Físico', color: '#16a34a' }">
                        <td class="px-6 text-left whitespace-nowrap text-white w-[20%]" style="background-color: #16a34a;">
                            <div style="display: flex; align-items: baseline;">
                                <span style="font-size: 3.5rem; line-height: 1; font-weight: 800; margin-right: 2px;">F</span>
                                <span style="font-size: 1.25rem; font-weight: 600;">ísico</span>
                            </div>
                        </td>
                        <td class="p-0 align-middle text-white w-[20%]" style="background-color: #4ade80; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;">
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Desempenho</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Auto-conhecimento</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">Bem-estar Físico</div>
                            </div>
                        </td>
                        <td class="p-0 align-middle" style="background-color: #bbf7d0; color: #166534; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;" class="cursor-pointer">
                                <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'F1', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('F1', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    F1. Praticar actividade física que promova o desenvolvimento e manutenção da agilidade, flexibilidade e destreza de forma adequada à sua idade, capacidade e limitações.
                                </div>

                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'F2', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('F2', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        F2. Conhecer e aceitar o desenvolvimento e amadurecimento do seu corpo com naturalidade
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'F3', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('F3', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        F3. Conhecer as características fisiológicas do corpo masculino e feminino e a sua relação com o comportamento e necessidades individuais
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex;">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'F4', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('F4', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        F4. Cultivar um estilo de vida saudável e equilibrado – alimentação, actividade física e repouso –, adaptado a cada fase do seu desenvolvimento
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'F5', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('F5', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        F5. Cuidar e valorizar o seu corpo de acordo com os padrões de saúde, revelando aprumo
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'F6', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('F6', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        F6. Identificar e evitar, na vida quotidiana, os comportamentos de risco relacionados com a segurança física e consumo de substância
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Linha 2: Vermelho - Afectivo -->
                    <tr style="height: 16.66%; min-height: 120px;" class="border-b-8 border-[#FAF7F5]" x-data="{ category: 'Afectivo', color: '#dc2626' }">
                        <td class="px-6 text-left whitespace-nowrap text-white w-[20%]" style="background-color: #dc2626;">
                            <div style="display: flex; align-items: baseline;">
                                <span style="font-size: 3.5rem; line-height: 1; font-weight: 800; margin-right: 2px;">A</span>
                                <span style="font-size: 1.25rem; font-weight: 600;">fectivo</span>
                            </div>
                        </td>
                        <td class="p-0 align-middle text-white w-[20%]" style="background-color: #f87171; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;">
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Relacionamento e Sensibilidade</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Equilibrio emocional</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">Auto-estima</div>
                            </div>
                        </td>
                        <td class="p-0 align-middle" style="background-color: #fecaca; color: #991b1b; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;" class="cursor-pointer">
                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'A1', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('A1', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        A1. Valorizar e demonstrar sensibilidade nas suas relações afectivas, de modo consequente com a opção de vida assumida
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'A2', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('A2', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        A2. Respeitar a existência de várias sensibilidades estéticas e artísticas, formando a sua opinião com sentido crítico
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'A3', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('A3', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        A3. Assumir a própria sexualidade aceitando a complementaridade Homem / Mulher e vivê-la como expressão responsável de amor
                                    </div>
                                </div>

                                <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'A4', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('A4', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    A4. Ser capaz de identificar, compreender e expressar as suas emoções, tendo em conta o contexto e os sentimentos dos outros
                                </div>

                                <div style="flex: 1; display: flex;">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'A5', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('A5', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        A5. Reconhecer e aceitar as características da sua personalidade, mantendo uma atitude de aperfeiçoamento constante
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'A6', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('A6', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        A6. Valorizar as próprias capacidades, superando limitações e adoptando uma atitude positiva perante a vida
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Linha 3: Azul - Carácter -->
                    <tr style="height: 16.66%; min-height: 120px;" class="border-b-8 border-[#FAF7F5]" x-data="{ category: 'Carácter', color: '#2563eb' }">
                        <td class="px-6 text-left whitespace-nowrap text-white w-[20%]" style="background-color: #2563eb;">
                            <div style="display: flex; align-items: baseline;">
                                <span style="font-size: 3.5rem; line-height: 1; font-weight: 800; margin-right: 2px;">C</span>
                                <span style="font-size: 1.25rem; font-weight: 600;">arácter</span>
                            </div>
                        </td>
                        <td class="p-0 align-middle text-white" style="background-color: #60a5fa; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;">
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Autonomia</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Responsabilidade</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">Coerência</div>
                            </div>
                        </td>
                        <td class="p-0 align-middle" style="background-color: #bfdbfe; color: #1e40af; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;" class="cursor-pointer">
                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C1', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C1', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        C1. Possuir e desenvolver um quadro de valores que são fruto de uma opção consciente
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C2', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C2', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        C2. Ser capaz de formular e construir as suas próprias opções, assumindo-as com clareza
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C3', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C3', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        C3. Mostrar-se responsável pelo seu desenvolvimento, colocando a si próprio objectivos de progressão pessoal
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C4', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C4', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        C4. Demonstrar empenho e vontade de agir, assumindo as suas responsabilidades em todos os projectos que enceta, estabelecendo prioridades e respeitando-as
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C5', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C5', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        C5. Demonstrar perseverança nos momentos de dificuldade, procurando ultrapassá-los com optimismo
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C6', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C6', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        C6. Ser consequente com as opções que toma, assumindo a responsabilidade pelos seus actos
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex;">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C7', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C7', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        C7. Ser consistente e convicto na defesa das suas ideias e valores
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'C8', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('C8', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        C8. Dar testemunho, agindo em coerência com o seu sistema de valores
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Linha 4: Roxo - Espiritual -->
                    <tr style="height: 16.66%; min-height: 120px;" class="border-b-8 border-[#FAF7F5]" x-data="{ category: 'Espiritual', color: '#9333ea' }">
                        <td class="px-6 text-left whitespace-nowrap text-white w-[20%]" style="background-color: #9333ea;">
                            <div style="display: flex; align-items: baseline;">
                                <span style="font-size: 3.5rem; line-height: 1; font-weight: 800; margin-right: 2px;">E</span>
                                <span style="font-size: 1.25rem; font-weight: 600;">spiritual</span>
                            </div>
                        </td>
                        <td class="p-0 align-middle text-white" style="background-color: #c084fc; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;">
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Descoberta</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Aprofundamento</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">Serviço</div>
                            </div>
                        </td>
                        <td class="p-0 align-middle" style="background-color: #e9d5ff; color: #6b21a8; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;" class="cursor-pointer">
                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E1', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E1', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        E1 Conhecer e compreender o modo como Deus se deu a conhecer à humanidade, propondo-lhe um Projecto de Felicidade Plena (História da Salvação).
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E2', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E2', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        E2 Conhecer em profundidade a mensagem e a proposta de Jesus Cristo (Mistério da Encarnação e Mistério Pascal).
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E3', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E3', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        E3 Reconhecer que a pertença à Igreja é um sinal de Deus no mundo de hoje (Igreja Sacramento Universal de Salvação).
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E4', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E4', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        E4 Aprofundar os hábitos de oração pessoal e assumir-se como membro activo da Igreja na celebração comunitária.
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E5', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E5', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        E5 Integrar na sua vida os valores do Evangelho, vivendo as propostas da Igreja.
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E6', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E6', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        E6 Conhecer as principais religiões distinguindo e valorizando a identidade da Igreja Católica.
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex;">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E7', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E7', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        E7 Testemunhar que a presença de Deus no mundo dignifica a vida humana e a natureza
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'E8', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('E8', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        E8 Viver o compromisso Cristão como missão no mundo em todas as dimensões (humanas, sociais, económicas, culturais e políticas).
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Linha 5: Laranja - Intelectual -->
                    <tr style="height: 16.66%; min-height: 120px;" class="border-b-8 border-[#FAF7F5]" x-data="{ category: 'Intelectual', color: '#f97316' }">
                        <td class="px-6 text-left whitespace-nowrap text-white w-[20%]" style="background-color: #f97316;">
                            <div style="display: flex; align-items: baseline;">
                                <span style="font-size: 3.5rem; line-height: 1; font-weight: 800; margin-right: 2px;">I</span>
                                <span style="font-size: 1.25rem; font-weight: 600;">ntelectual</span>
                            </div>
                        </td>
                        <td class="p-0 align-middle text-white" style="background-color: #fdba74; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;">
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Procura do conhecimento</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Resolução de problemas</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">Creatividade e Expressão</div>
                            </div>
                        </td>
                        <td class="p-0 align-middle" style="background-color: #ffedd5; color: #9a3412; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;" class="cursor-pointer">
                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'I1', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('I1', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        I1. Procurar de forma activa e continuada novos saberes e vivências, como forma de contribuir para o seu crescimento pessoal
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'I2', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('I2', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        I2. Conhecer e utilizar formas adequadas de recolha e tratamento de informação e, dentro dessas, distinguir o essencial do acessório
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'I3', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('I3', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        I3. Definir o seu itinerário de formação preocupando-se em mantê-lo actualizado
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'I4', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('I4', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        I4. Adaptar-se e superar novas situações, avaliando-as à luz de experiências anteriores e conhecimentos adquiridos
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'I5', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('I5', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        I5. Analisar os problemas de forma crítica, sugerindo e aplicando estratégias de resolução
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex;">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'I6', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('I6', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        I6. Ser capaz de utilizar conhecimentos, percepções e intuições na criação de novas ideias e obras, mantendo um espírito aberto e inovador
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'I7', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('I7', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        I7 Expressar ideias e emoções de forma lógica e criativa, adaptada ao(s) destinatário(s) e utilizando os meios adequados.
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Linha 6: Amarelo - Social -->
                    <tr style="height: 16.66%; min-height: 120px;" x-data="{ category: 'Social', color: '#eab308' }">
                        <td class="px-6 text-left whitespace-nowrap text-white w-[20%]" style="background-color: #eab308;">
                            <div style="display: flex; align-items: baseline;">
                                <span style="font-size: 3.5rem; line-height: 1; font-weight: 800; margin-right: 2px;">S</span>
                                <span style="font-size: 1.25rem; font-weight: 600;">ocial</span>
                            </div>
                        </td>
                        <td class="p-0 align-middle text-white" style="background-color: #fde047; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;">
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Exercer ativamente Cidadania</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.3);">Solidariedade e tolerância</div>
                                <div style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">Iteracções e cooperação</div>
                            </div>
                        </td>
                        <td class="p-0 align-middle" style="background-color: #fef9c3; color: #854d0e; height: 100%;">
                            <div style="display: flex; flex-direction: column; height: 100%; min-height: 120px;" class="cursor-pointer">
                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'S1', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('S1', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        S1. Conhecer e exercer os seus direitos e deveres enquanto cidadão
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'S2', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('S2', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        S2. Participar activa e conscientemente nos vários espaços sociais onde se insere, intervindo de uma forma informada, respeitadora e construtiva
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'S3', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('S3', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        S3. Respeitar as regras democráticas e assumir como suas as decisões tomadas colectivamente
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'S4', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('S4', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        S4. Assumir que é parte da sociedade onde se insere, agindo numa perspectiva de serviço libertador e de construção de futuro
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'S5', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('S5', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        S5. Usar de empatia na forma de comunicar com os outros, demonstrando tolerância e respeito perante outros pontos de vista
                                    </div>
                                </div>

                                <div style="flex: 1; display: flex;">
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'S6', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('S6', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem; border-right: 1px solid rgba(0,0,0,0.1);">
                                        S6. Mostrar capacidade de relacionamento e trabalho em equipa, contribuindo activamente para o sucesso do colectivo através do desempenho com competência do seu papel
                                    </div>
                                    <div @click="$dispatch('open-content-modal', { category: category, content: $el.innerText.trim(), reference: 'S7', color: color })" class="hover:bg-white/30 transition duration-150 {{ in_array('S7', $approvedRefs) ? 'line-through decoration-2 opacity-50' : '' }}" style="flex: 1; display: flex; align-items: center; padding: 1rem 1.5rem;">
                                        S7. Assumir papéis de liderança, de forma equilibrada, tendo em conta as suas necessidades e as do grupo
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
