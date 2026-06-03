<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased" style="background-color: oklch(98% 0.016 73.684); color: oklch(40% 0.123 38.172);">

<div class="min-h-screen flex">

    {{-- Coluna esquerda: Carousel --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden"
         x-data="{ current: 0, total: 3 }"
         x-init="setInterval(() => current = (current + 1) % total, 5000)">

        {{-- Slide 1 --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
             x-bind:class="current === 0 ? 'opacity-100' : 'opacity-0'">
            <img src="/images/down.jpg" alt="Foto 1" class="w-full h-full object-cover" />
        </div>

        {{-- Slide 2 --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
             x-bind:class="current === 1 ? 'opacity-100' : 'opacity-0'">
            <img src="/images/jota-joti-2024.jpg" alt="Foto 2" class="w-full h-full object-cover" />
        </div>

        {{-- Slide 3 --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
             x-bind:class="current === 2 ? 'opacity-100' : 'opacity-0'">
            <img src="/images/agru.jpg" alt="Foto 3" class="w-full h-full object-cover" />
        </div>

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/40"></div>

        {{-- Texto sobre o carousel --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center z-10 text-white px-10 text-center">
            <img src="/images/logo-banana.jpg" alt="Logo" class="w-24 h-24 rounded-full shadow-xl mb-4 object-cover" />
            <h1 class="text-3xl font-bold drop-shadow-lg">{{ __('Corpo Nacional de Escutas') }}</h1>
            <p class="mt-2 text-lg text-white/80 drop-shadow">{{ __('Agrupamento 542 · Entroncamento') }}</p>
        </div>

        {{-- Indicadores --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
            <template x-for="i in total" :key="i">
                <button
                    x-on:click="current = i - 1"
                    x-bind:class="current === i - 1 ? 'bg-white w-6' : 'bg-white/40 w-2'"
                    class="h-2 rounded-full transition-all duration-300"
                ></button>
            </template>
        </div>
    </div>

    {{-- Coluna direita: Form --}}
    <div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-8 py-12"
         style="background-color: oklch(98% 0.016 73.684);">
        {{ $slot }}
    </div>

</div>

@livewireScripts
</body>
</html>
