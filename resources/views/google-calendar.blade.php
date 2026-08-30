{{--
    Uso: <x-google-calendar calendar-id="abc123@group.calendar.google.com" />

    Parâmetros opcionais:
    - title: título mostrado no cabeçalho do cartão (default "Calendário")
    - height: altura do calendário em px (default 500)
    - timezone: fuso horário, ex "Europe/Lisbon" (default null = usa o do calendário)
--}}
@props([
    'calendarId',
    'title' => 'Calendário',
    'height' => 500,
    'timezone' => null,
])

@php
    $params = http_build_query(array_filter([
        'src' => $calendarId,
        'ctz' => $timezone,
        'mode' => 'MONTH',
        'showTitle' => '0',
        'showPrint' => '0',
        'showCalendars' => '0',
        'showTz' => '0',
    ]));

    $embedUrl = "https://calendar.google.com/calendar/embed?{$params}";
@endphp

<div class="bg-white rounded-[24px] shadow-sm border border-[#E4D5C3] p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-[#776246] uppercase tracking-widest">{{ $title }}</h3>
        <a href="https://calendar.google.com/calendar/u/0?cid={{ urlencode($calendarId) }}" target="_blank"
           class="text-xs font-semibold text-[#776246] hover:text-[#B5432A] inline-flex items-center gap-1">
            Abrir no Google Calendar
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <div class="rounded-2xl overflow-hidden border border-[#E4D5C3]">
        <iframe
            src="{{ $embedUrl }}"
            style="border: 0"
            width="100%"
            height="{{ $height }}"
            frameborder="0"
            scrolling="no"
            loading="lazy"
        ></iframe>
    </div>
</div>
