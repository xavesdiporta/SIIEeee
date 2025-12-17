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
    <style>
        [x-cloak] { display: none !important; }
        #calendar {
            max-width: 100%;
            background-color: #3E2D1B;
            padding: 1rem;
            border-radius: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .fc-toolbar-title {
            @apply text-xl font-semibold text-gray-800;
        }

        .fc-daygrid-event {
            background-color: #776246 !important;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            padding: 2px 4px;
            color: white;
        }

        .fc-daygrid-day:hover {
            background-color: #3E2D1B;
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#FAF7F5] text-[#665039]">
<x-banner />

<div class="min-h-screen bg-[#FAF7F5]">
    @include('components.navigation-menu')

    <div class="flex-1 ml-64">
        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow-sm rounded-2xl mx-6 mt-6">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-6 pr-6" style="background-color: #3E2D1B;">
            <div class="bg-[#F2ECE7] rounded-[30px] py-6 pr-6">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>


@stack('modals')

@livewireScripts
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para ajustar o tamanho dos inputs com margem de segurança
        function adjustInputWidth(input) {
            input.style.width = (input.value.length + 0.60) + 'ch';
        }

        // Ajusta todos os inputs com a classe auto-size
        document.querySelectorAll('.auto-size').forEach(input => {
            adjustInputWidth(input);

            input.addEventListener('change', () => adjustInputWidth(input));
            input.addEventListener('input', () => adjustInputWidth(input));
            input.addEventListener('focus', () => adjustInputWidth(input));
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt', // FullCalendar suporta português
            selectable: true,
            events: '/api/events', // Rota Laravel para retornar os eventos
            dateClick: function(info) {
                // Ex: Redireciona com query string para abrir o modal
                window.location.href = "{{ route('dashboard') }}?modal=add-link&date=" + info.dateStr;
            },
            eventClick: function(info) {
                alert('Evento: ' + info.event.title);
            }
        });

        calendar.render();
    });
</script>

</body>
</html>
