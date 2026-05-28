<div class="relative isolate overflow-hidden px-6 py-24 sm:py-24 lg:overflow-visible lg:px-0">
    <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-start lg:gap-y-10">

        <div class="lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
            <div class="lg:pr-4">
                <div class="lg:max-w-lg">
                    <p class="text-base font-semibold leading-7 text-secondary">{{ __('A Nossa História') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
                        {{ __('Agrupamento 542 · Entroncamento') }}
                    </h1>
                    <p class="mt-6 text-xl leading-8">
                        {{ __('O Agrupamento 542 do Entroncamento é uma comunidade escutista com raízes profundas na cidade do Entroncamento. Ao longo de décadas, temos formado jovens com valores de serviço, fraternidade e responsabilidade, deixando uma marca duradoura na nossa comunidade.') }}
                    </p>
                    <p class="mt-8">
                        {{ __('Desde a fundação do agrupamento, centenas de jovens passaram pelas nossas fileiras, levando consigo os valores do escutismo para a vida adulta. O nosso agrupamento faz parte do Corpo Nacional de Escutas, o maior movimento escutista católico de Portugal.') }}
                    </p>
                    <p class="mt-4">
                        {{ __('Hoje, o Agrupamento 542 continua ativo, com secções para todas as faixas etárias, promovendo o crescimento pessoal, o espírito de aventura e o serviço à comunidade entroncamentense e além.') }}
                    </p>

                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="rounded-lg bg-base-200 p-4 text-center">
                            <p class="text-3xl font-bold text-secondary">542</p>
                            <p class="text-sm text-base-content/70 mt-1">{{ __('Número do Agrupamento') }}</p>
                        </div>
                        <div class="rounded-lg bg-base-200 p-4 text-center">
                            <p class="text-3xl font-bold text-secondary">CNE</p>
                            <p class="text-sm text-base-content/70 mt-1">{{ __('Corpo Nacional de Escutas') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Foto do agrupamento - substitui pelo caminho da tua imagem --}}
        <div class="-ml-12 -mt-12 p-12 lg:sticky lg:top-4 lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:overflow-hidden">
            <img class="w-[48rem] max-w-none rounded-xl shadow-xl ring-1 ring-base-content/10 sm:w-[36rem]"
                 src="/images/agrupamento/historia.jpg"
                 alt="Foto histórica do Agrupamento 542 do Entroncamento" />
        </div>

    </div>
</div>
