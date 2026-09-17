<nav x-data="{ open: false }"
     class="fixed left-0 top-0 h-full"
     :class="{'w-64': !open, 'w-full md:w-64': open}"
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

    <!-- Mobile Menu Toggle -->
    <div class="md:hidden absolute right-2 top-2">
        <button @click="open = !open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="h-full flex flex-col overflow-y-auto">
        <!-- Logo -->
        <div class="p-4">
            <div class="px-4 pb-4 border-b" style="border-color: {{ $borderColor }};">
                <a href="{{ route('dashboard') }}">
                    <x-application-mark class="block h-9 w-auto" />
                </a>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-grow pl-4 space-y-2">
            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="block w-full">
                {{ __('A minha Área') }}
            </x-nav-link>
            <x-nav-link href="{{ route('allcalendar')}}" :active="request()->routeIs('allcalendar')" class="block w-full">
                {{ __('O meu Progresso') }}
            </x-nav-link>
            @if(Auth::user()->seccao === 'cla' || (Auth::user()->is_admin ?? false))
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
            <div class="px-4 pt-4 border-t" style="border-color: {{ $borderColor }};">
                <!-- Settings Dropdown (opens upward) -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <div @click="open = !open" class="cursor-pointer">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button class="flex flex-col items-start w-full text-sm border-2 border-transparent rounded-md focus:outline-none focus:border-gray-300 transition">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    <span class="ml-2 text-gray-500 dark:text-gray-400">{{ Auth::user()->name }}</span>
                                    <svg class="ml-2 -mr-0.5 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </div>
                                @if(Auth::user()->employeeGroup)
                                    <span class="ml-10 text-sm text-gray-400">{{ Auth::user()->employeeGroup->name }}</span>
                                @endif
                            </button>
                        @else
                            <span class="inline-flex rounded-md w-full">
                                <button type="button" class="inline-flex flex-col items-start w-full px-8 py-3 border border-transparent text-md leading-4 font-medium rounded-md text-gray-500 dark:text-white dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150" style="background-color: transparent;" onmouseover="this.style.backgroundColor='{{ $hoverBg }}'" onmouseout="this.style.backgroundColor='transparent'">
                                    <div class="flex items-center w-full justify-between">
                                        <span>{{ Auth::user()->name }}</span>
                                        <svg class="ms-2 -me-0.5 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </div>
                                    @if(Auth::user()->employeeGroup)
                                        <span class="text-sm text-gray-400">{{ Auth::user()->employeeGroup->name }}</span>
                                    @endif
                                </button>
                            </span>
                        @endif
                    </div>

                    <!-- Dropdown content (opens UPWARD) -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute bottom-full left-0 mb-2 w-48 rounded-md shadow-lg z-50 origin-bottom-left"
                         style="display: none;"
                         @click="open = false">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-600">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                                 @click.prevent="$root.submit();">
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
