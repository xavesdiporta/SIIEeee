<div x-data="{ open: false }">

    <!-- Mobile Header/Bar (Visível apenas em Mobile) -->
    <div class="md:hidden flex items-center justify-between px-4 py-3 border-b text-white shadow-sm"
         style="background-color: {{ match(Auth::user()->seccao ?? 'cla') {
             'lobitos'      => '#7A6520',
             'exploradores' => '#2D5A3D',
             'pioneiros'    => '#2A3D6B',
             'cla'          => '#3E2D1B',
             default        => '#3E2D1B',
         } }}; border-color: {{ match(Auth::user()->seccao ?? 'cla') {
             'lobitos'      => '#9B8432',
             'exploradores' => '#3E7A52',
             'pioneiros'    => '#3D5490',
             'cla'          => '#5C4B3A',
             default        => '#5C4B3A',
         } }};">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-mark class="block h-8 w-auto" />
        </a>
        <button @click="open = !open" type="button" class="p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/20 text-white hover:bg-black/10">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Backdrop escuro para Mobile -->
    <div x-show="open"
         x-cloak
         @click="open = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 md:hidden">
    </div>

    <!-- Sidebar Drawer (Fixa em Desktop, Deslizante em Mobile) -->
    <nav class="fixed left-0 top-0 h-full w-64 z-50 transform transition-transform duration-300 ease-in-out md:translate-x-0"
         :class="open ? 'translate-x-0' : '-translate-x-full'"
         style="background-color: {{ match(Auth::user()->seccao ?? 'cla') {
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

        <div class="h-full flex flex-col overflow-y-auto">
            <!-- Logo (Escondido em mobile pois já está na top bar) -->
            <div class="p-4 hidden md:block">
                <div class="px-4 pb-4 border-b" style="border-color: {{ $borderColor }};">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>
            </div>

            <!-- Botão fechar dentro do drawer no Mobile -->
            <div class="p-4 md:hidden flex justify-end border-b" style="border-color: {{ $borderColor }};">
                <button @click="open = false" class="text-white p-1 hover:bg-black/10 rounded-md">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-grow p-4 space-y-2">
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="block w-full">
                    {{ __('A minha Área') }}
                </x-nav-link>

                @if($seccao === 'cla' || (Auth::user()->is_admin ?? false))
                    <x-nav-link href="{{ route('allcalendar')}}" :active="request()->routeIs('allcalendar')" class="block w-full">
                        {{ __('O meu Progresso') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('cla.noites-campo')}}" :active="request()->routeIs('cla.noites-campo*')" class="block w-full">
                        {{ __('Noites de Campo') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('cla.horasmar')}}" :active="request()->routeIs('cla.horasmar*')" class="block w-full">
                        {{ __('Horas de Mar') }}
                    </x-nav-link>
                @endif
            </div>

            <!-- User Menu Section -->
            <div class="p-4">
                <div class="px-2 pt-4 border-t" style="border-color: {{ $borderColor }};">
                    <!-- Settings Dropdown (opens upward) -->
                    <div class="relative" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
                        <div @click="userMenuOpen = !userMenuOpen" class="cursor-pointer">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex flex-col items-start w-full text-sm border-2 border-transparent rounded-md focus:outline-none focus:border-gray-300 transition">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                        <span class="ml-2 text-white font-medium truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                                        <svg class="ml-2 -mr-0.5 h-4 w-4 text-white transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </div>
                                    @if(Auth::user()->employeeGroup)
                                        <span class="ml-10 text-xs text-gray-300">{{ Auth::user()->employeeGroup->name }}</span>
                                    @endif
                                </button>
                            @else
                                <span class="inline-flex rounded-md w-full">
                                    <button type="button" class="inline-flex flex-col items-start w-full px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-gray-200 focus:outline-none transition ease-in-out duration-150" style="background-color: transparent;" onmouseover="this.style.backgroundColor='{{ $hoverBg }}'" onmouseout="this.style.backgroundColor='transparent'">
                                        <div class="flex items-center w-full justify-between">
                                            <span class="truncate">{{ Auth::user()->name }}</span>
                                            <svg class="ms-2 -me-0.5 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                        </div>
                                        @if(Auth::user()->employeeGroup)
                                            <span class="text-xs text-gray-300 mt-1">{{ Auth::user()->employeeGroup->name }}</span>
                                        @endif
                                    </button>
                                </span>
                            @endif
                        </div>

                        <!-- Dropdown content (opens UPWARD) -->
                        <div x-show="userMenuOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute bottom-full left-0 mb-2 w-full rounded-md shadow-lg z-50 origin-bottom-left"
                             @click="userMenuOpen = false">
                            <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
