<div class="hero min-h-screen bg-base-100 relative overflow-hidden">

    {{-- Carousel de fundo --}}
    <div class="absolute inset-0 z-0" x-data="{ current: 0, total: 3 }" x-init="setInterval(() => current = (current + 1) % total, 5000)">

        {{-- Slide 1 --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
             x-bind:class="current === 0 ? 'opacity-100' : 'opacity-0'">
            <img src="/images/down.jpg"
                 alt="Foto do agrupamento 1"
                 class="w-full h-full object-cover" />
        </div>

        {{-- Slide 2 --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
             x-bind:class="current === 1 ? 'opacity-100' : 'opacity-0'">
            <img src="/images/jota-joti-2024.jpg"
                 alt="Foto do agrupamento 2"
                 class="w-full h-full object-cover" />
        </div>

        {{-- Slide 3 --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
             x-bind:class="current === 2 ? 'opacity-100' : 'opacity-0'">
            <img src="/images/agru.jpg"
                 alt="Foto do agrupamento 3"
                 class="w-full h-full object-cover" />
        </div>

        {{-- Overlay escuro para legibilidade do texto --}}
        <div class="absolute inset-0 bg-black/55"></div>

        {{-- Indicadores do carousel --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2 z-10">
            <template x-for="i in total" :key="i">
                <button
                    x-on:click="current = i - 1"
                    x-bind:class="current === i - 1 ? 'bg-white w-6' : 'bg-white/40 w-2'"
                    class="h-2 rounded-full transition-all duration-300"
                    :aria-label="`Ir para slide ${i}`"
                ></button>
            </template>
        </div>
    </div>

    {{-- Conteúdo do hero --}}
    <div class="hero-content flex-col text-center relative z-10 text-white px-4">
        <div class="max-w-3xl">
            <div class="badge badge-secondary badge-lg mb-4 text-white border-0">
                {{ __('Agrupamento 542 · Entroncamento') }}
            </div>
            <h1 class="text-5xl sm:text-7xl font-bold drop-shadow-lg">
                {{ __('Corpo Nacional de Escutas') }}
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-white/85 max-w-xl mx-auto drop-shadow">
                {{ __('Bem-vindo ao sistema de gestão do Agrupamento 542 do Entroncamento. Aqui podes acompanhar o progresso, atividades e muito mais.') }}
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}" class="btn btn-secondary btn-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>
                    </svg>
                    {{ __('Entrar no Sistema') }}
                </a>
                <a href="#features" class="btn btn-outline btn-lg text-white border-white hover:bg-white hover:text-black">
                    {{ __('Saber Mais') }}
                </a>
            </div>
        </div>
    </div>

</div>
