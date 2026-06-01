<div class="hero min-h-screen bg-base-100 relative" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); width: 100vw;">

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
            <div class="badge badge-lg mb-4 border-0 text-white" style="background-color: #3b6e3b;">
                {{ __('Agrupamento 542 · Entroncamento') }}
            </div>
            <h1 class="text-5xl sm:text-7xl font-bold drop-shadow-lg">
                {{ __('Corpo Nacional de Escutas') }}
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-white/85 max-w-xl mx-auto drop-shadow">
                {{ __('Bem-vindo ao site do Agrupamento 542 do Entroncamento.') }}
            </p>
        </div>
    </div>

</div>
