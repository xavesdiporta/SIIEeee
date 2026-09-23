<x-guest-layout>
    <div class="w-full max-w-md mx-auto">

        {{-- Logo e título --}}
        <div class="flex flex-col items-center mb-10">
            <img src="/images/logo-banana.png" alt="Agrupamento 542"
                 class="w-20 h-20 rounded-full object-cover shadow-md mb-4" />
            <h2 class="text-3xl font-bold" style="color: oklch(22.45% 0.075 37.85);">{{ __('Bem-vindo') }}</h2>
            <p class="mt-1 text-sm" style="color: oklch(55% 0.08 38.172);">{{ __('Inicia sessão para acederes ao sistema.') }}</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
        <div class="mb-4 font-medium text-sm" style="color: oklch(43% 0.095 166.913);">
            {{ $value }}
        </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium mb-1.5"
                       style="color: oklch(40% 0.123 38.172);">
                    {{ __('Email') }}
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       class="w-full rounded-lg px-4 py-3 text-base outline-none transition-all"
                       style="background-color: oklch(96% 0.02 75.164);
                              color: oklch(30% 0.08 38.172);
                              border: 1.5px solid oklch(88% 0.05 70.697);"
                       onfocus="this.style.border='1.5px solid oklch(46.44% 0.111 37.85)'; this.style.boxShadow='0 0 0 3px oklch(46.44% 0.111 37.85 / 0.15)'"
                       onblur="this.style.border='1.5px solid oklch(88% 0.05 70.697)'; this.style.boxShadow='none'" />
            </div>

            {{-- Password com Botão para Mostrar / Ocultar --}}
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-medium mb-1.5"
                       style="color: oklch(40% 0.123 38.172);">
                    {{ __('Password') }}
                </label>
                <div class="relative">
                    <input id="password"
                           :type="showPassword ? 'text' : 'password'"
                           name="password"
                           required autocomplete="current-password"
                           class="w-full rounded-lg px-4 py-3 pr-11 text-base outline-none transition-all"
                           style="background-color: oklch(96% 0.02 75.164);
                                  color: oklch(30% 0.08 38.172);
                                  border: 1.5px solid oklch(88% 0.05 70.697);"
                           onfocus="this.style.border='1.5px solid oklch(46.44% 0.111 37.85)'; this.style.boxShadow='0 0 0 3px oklch(46.44% 0.111 37.85 / 0.15)'"
                           onblur="this.style.border='1.5px solid oklch(88% 0.05 70.697)'; this.style.boxShadow='none'" />

                    <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-md transition-colors hover:opacity-80"
                            style="color: oklch(50% 0.07 38.172);"
                            aria-label="{{ __('Mostrar ou ocultar password') }}">
                        {{-- Ícone Olho Aberto --}}
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{-- Ícone Olho Fechado --}}
                        <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a9.954 9.954 0 013.122-.563c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.28 3.59m-4.116 4.116A3 3 0 0112 15a3 3 0 01-2.121-.879m3.121-3.121A3 3 0 0012 9" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember + Forgot --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="rounded"
                           style="accent-color: oklch(22.45% 0.075 37.85); width: 1rem; height: 1rem;" />
                    <span class="text-sm" style="color: oklch(50% 0.07 38.172);">{{ __('Lembrar-me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm transition"
                       style="color: oklch(46.44% 0.111 37.85);"
                       onmouseover="this.style.color='oklch(22.45% 0.075 37.85)'"
                       onmouseout="this.style.color='oklch(46.44% 0.111 37.85)'">
                        {{ __('Esqueceste a password?') }}
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3 rounded-lg text-base font-semibold transition-all hover:opacity-90 active:scale-[0.99] mt-2"
                    style="background-color: oklch(22.45% 0.075 37.85);
                           color: oklch(92% 0.05 70.697);
                           letter-spacing: 0.01em;">
                {{ __('Entrar') }}
            </button>

        </form>

        {{-- Footer --}}
        <p class="mt-10 text-center text-xs" style="color: oklch(65% 0.05 38.172);">
            {{ __('Agrupamento 542 · Entroncamento · CNE') }}
        </p>

    </div>
</x-guest-layout>
