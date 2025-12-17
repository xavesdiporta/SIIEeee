<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body
    style="background-color: oklch(98% 0.016 73.684);
               color: oklch(40% 0.123 38.172);
               font-family: 'Figtree', sans-serif;"
>
<div class="font-sans antialiased"
     style="color: oklch(40% 0.123 38.172);
                    background-color: oklch(98% 0.016 73.684);
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;">
    {{ $slot }}
</div>

@livewireScripts
</body>
</html>
