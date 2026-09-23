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

        <div class="h-full flex flex-col justify-between overflow-y-auto">

            <!-- Parte Superior: Logo e Links de Navegação -->
            <div class="flex-grow">
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

                <!-- Links de Navegação -->
                <div class="p-4 space-y-2">
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
            </div>

            <!-- Parte Inferior: Perfil do Utilizador (Desdobrável Inline) -->
            <div class="p-4 border-t shrink-0" style="border-color: {{ $borderColor }};">
                <div x-data="{ userMenuOpen: false }">

                    <!-- Cartão do Utilizador (Trigger) -->
                    <button @click="userMenuOpen = !userMenuOpen"
                            type="button"
                            class="w-full flex items-center justify-between p-2 rounded-xl transition-colors text-left group"
                            style="background-color: transparent;"
                            onmouseover="this.style.backgroundColor='rgba(0,0,0,0.15)'"
                            onmouseout="this.style.backgroundColor='transparent'">

                        <div class="flex items-center gap-2.5 min-w-0">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="h-8 w-8 rounded-full object-cover shrink-0 border border-white/20"
                                     src="{{ Auth::user()->profile_photo_url }}"
                                     alt="{{ Auth::user()->name }}" />
                            @else
                                <div class="h-8 w-8 rounded-full bg-white/10 flex items-center justify-center text-white font-bold text-xs shrink-0 border border-white/20">
                                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                                @if(Auth::user()->employeeGroup)
                                    <p class="text-[10px] text-white/70 truncate mt-0.5">{{ Auth::user()->employeeGroup->name }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Seta Indicadora -->
                        <svg class="h-4 w-4 text-white/70 transition-transform duration-200 shrink-0 ml-1"
                             :class="{ 'rotate-180': userMenuOpen }"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Submenu Desdobrável Inline (Expande para baixo de forma limpa) -->
                    <div x-show="userMenuOpen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-2 pt-2 space-y-1 border-t border-white/10">

                        <!-- Opção: Perfil -->
                        <a href="{{ route('profile.show') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-white/90 rounded-lg transition-colors hover:bg-black/20 hover:text-white">
                            <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span>{{ __('O meu Perfil') }}</span>
                        </a>

                        <!-- Opção: Sair -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <a href="{{ route('logout') }}"
                               @click.prevent="$root.submit();"
                               class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-red-200 hover:text-red-100 rounded-lg transition-colors hover:bg-red-500/20">
                                <svg class="w-4 h-4 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12" />
                                </svg>
                                <span>{{ __('Terminar Sessão') }}</span>
                            </a>
                        </form>

                    </div>

                </div>
            </div>

        </div>
    </nav>
</div>
