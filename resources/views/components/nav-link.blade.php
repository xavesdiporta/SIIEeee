@props(['active'])

@php
    $classes = ($active ?? false)
                    ? 'relative inline-flex items-center w-full px-6 py-4 text-[15px] font-medium leading-5 text-[#3E2D1B] before:content-[""] before:absolute before:inset-0 before:bg-[#FAF7F5] before:rounded-l-full before:right-0 before:z-0 focus:outline-none transition duration-150 ease-in-out'
                : 'group relative inline-flex items-center w-full px-6 py-4 text-[15px] font-medium leading-5 text-[#E4D5C3] hover:text-white hover:before:content-[""] hover:before:absolute hover:before:inset-0 hover:before:bg-[#776246]/30 hover:before:rounded-l-full hover:before:right-0 hover:before:z-0 after:content-[""] after:absolute after:w-4 after:h-4 after:-top-4 after:right-0 after:bg-transparent after:rounded-br-full after:shadow-[8px_8px_0_0_rgb(250,247,245)] after:hidden group-hover:after:block focus:outline-none transition-all duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="relative z-10 flex items-center">
        <svg class="w-6 h-6 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/>
        </svg>
        {{ $slot }}
    </span>
</a>
