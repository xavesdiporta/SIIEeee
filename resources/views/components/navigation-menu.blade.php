<nav x-data="{ open: false }"
     class="fixed left-0 top-0 h-full bg-[#3E2D1B]"
     :class="{'w-64': !open, 'w-full md:w-64': open}">


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
            <div class="px-4 pb-4 border-b border-[#5C4B3A]">
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
        </div>

        <!-- Teams and Settings Section -->
        <div class="p-4">
            <div class="px-4 pt-4 border-t border-[#5C4B3A]">
                <!-- Settings Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <div class="inline-flex items-center w-full">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button class="flex flex-col items-start w-full text-sm border-2 border-transparent rounded-md focus:outline-none focus:border-gray-300 transition">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                            <span class="ml-2 text-gray-500 dark:text-gray-400">{{ Auth::user()->name }}</span>
                                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </div>
                                        @if(Auth::user()->employeeGroup)
                                            <span class="ml-10 text-sm text-gray-400">{{ Auth::user()->employeeGroup->name }}</span>
                                        @endif
                                    </button>
                                @else
                                    <span class="inline-flex rounded-md w-full">
                                        <button type="button" class="inline-flex flex-col items-start w-full px-8 py-3 border border-transparent text-md leading-4 font-medium rounded-md text-gray-500 dark:text-white dark:hover:text-gray-300 focus:outline-none focus:bg-[#4E3D2B] active:bg-gray-50 transition ease-in-out duration-150">
                                            <div class="flex items-center w-full justify-between">
                                                <span>{{ Auth::user()->name }}</span>
                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </div>
                                            @if(Auth::user()->employeeGroup)
                                                <span class="text-sm text-gray-400">{{ Auth::user()->employeeGroup->name }}</span>
                                            @endif
                                        </button>
                                    </span>
                                @endif
                            </div>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-600">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                                 @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>
</nav>
