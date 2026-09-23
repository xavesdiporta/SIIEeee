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
        }

        #calendar .fc {
            --fc-border-color: #E4D5C3;
            --fc-today-bg-color: #FCEBE6;
            --fc-neutral-bg-color: #FAF7F5;
            font-family: inherit;
        }

        #calendar .fc-toolbar-title {
            font-size: 1rem;
            font-weight: 700;
            color: #3E2D1B;
            text-transform: capitalize;
        }

        @media (min-width: 640px) {
            #calendar .fc-toolbar-title {
                font-size: 1.125rem;
            }
        }

        #calendar .fc-col-header-cell {
            background-color: #FAF7F5;
            padding: 0.5rem 0;
        }

        #calendar .fc-col-header-cell-cushion {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #776246;
        }

        #calendar .fc-daygrid-day-number {
            font-size: 0.8rem;
            color: #3E2D1B;
            padding: 0.4rem;
        }

        #calendar .fc-day-today .fc-daygrid-day-number {
            background-color: #B5432A;
            color: #fff;
            border-radius: 9999px;
            width: 1.6rem;
            height: 1.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.2rem;
        }

        #calendar .fc-daygrid-day:hover {
            background-color: #FAF7F5;
            cursor: pointer;
        }

        #calendar .fc-daygrid-event {
            background-color: #B5432A;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            padding: 1px 6px;
            color: white;
        }

        #calendar .fc-button {
            background-color: #FAF7F5 !important;
            border: 1px solid #E4D5C3 !important;
            color: #776246 !important;
            box-shadow: none !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
        }

        #calendar .fc-button:hover {
            background-color: #F2ECE7 !important;
        }

        #calendar .fc-button-active {
            background-color: #3E2D1B !important;
            color: #fff !important;
        }
    </style>
</head>
<body class="font-sans antialiased" style="background-color: {{ match(Auth::user()->seccao ?? 'cla') {
         'lobitos'      => '#9B8432',
         'exploradores' => '#2D5A3D',
         'pioneiros'    => '#3D5490',
         'cla'          => '#5C4B3A',
         default        => '#5C4B3A',
     } }};">
<x-banner />

<div class="min-h-screen flex flex-col md:flex-row" style="background-color: {{ match(Auth::user()->seccao ?? 'cla') {
             'lobitos'      => '#9B8432',
             'exploradores' => '#2D5A3D',
             'pioneiros'    => '#3D5490',
             'cla'          => '#5C4B3A',
             default        => '#5C4B3A',
         } }};">

    {{-- Navigation Sidebar --}}
    @include('components.navigation-menu')

    {{-- Main Container --}}
    <div class="flex-1 md:ml-64 min-w-0 transition-all duration-300">
        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow-sm rounded-2xl mx-3 sm:mx-6 mt-3 sm:mt-6">
                <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-4 sm:py-6 px-3 sm:px-6 md:pl-0 md:pr-6" style="background-color: {{ match(Auth::user()->seccao ?? 'cla') {
                     'lobitos'      => '#7A6520',
                     'exploradores' => '#2D5A3D',
                     'pioneiros'    => '#2A3D6B',
                     'cla'          => '#3E2D1B',
                     default        => '#3E2D1B',
                 } }};">

            @php
                $seccao = Auth::user()->seccao ?? 'cla';
                $borderColor = match($seccao) {
                    'lobitos'      => '#9B8432',
                    'exploradores' => '#3E7A52',
                    'pioneiros'    => '#3D5490',
                    'cla'          => '#5C4B3A',
                    default        => '#5C4B3A',
                };
                $hoverBg = match($seccao) {
                    'lobitos'      => '#8B7428',
                    'exploradores' => '#366A45',
                    'pioneiros'    => '#344C7F',
                    'cla'          => '#4E3D2B',
                    default        => '#4E3D2B',
                };
            @endphp

            <div class="bg-[#F2ECE7] rounded-[20px] sm:rounded-[30px] p-4 sm:p-6">
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
        function adjustInputWidth(input) {
            input.style.width = (input.value.length + 0.60) + 'ch';
        }

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
        if (!calendarEl) return;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'dayGridMonth,listMonth',
            },
            selectable: true,
            events: '/api/events',
            dateClick: function (info) {
                const diaInput = document.getElementById('dia');
                if (diaInput) {
                    diaInput.value = info.dateStr;
                    diaInput.closest('form')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    diaInput.focus();
                }
            },
            eventClick: function (info) {
                if (info.event.url) {
                    window.open(info.event.url, '_blank');
                    info.jsEvent.preventDefault();
                }
            },
        });

        calendar.render();
    });
</script>
</body>
</html>
