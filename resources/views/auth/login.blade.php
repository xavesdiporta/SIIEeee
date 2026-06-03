<x-guest-layout>
    <div class="w-full max-w-md mx-auto">

        {{-- Logo mobile (só aparece em ecrãs pequenos) --}}
        <div class="flex flex-col items-center mb-8 lg:hidden">
            <img src="/images/logo-banana.jpg" alt="Agrupamento 542" class="w-20 h-20 rounded-full object-cover shadow-md" />
            <p class="mt-2 text-base font-semibold" style="color: oklch(40% 0.123 38.172);">
                {{ __('Agrupamento 542 · Entroncamento') }}
            </p>
        </div>

        {{-- Título --}}
        <div class="mb-8">
            <h2 class="text-3xl font-bold" style="color: oklch(22.45% 0.075 37.85);">{{ __('Bem-vindo') }}</h2>
            <p class="mt-1 text-base" style="color: oklch(55% 0.08 38.172);">{{ __('Inicia sessão para acederes ao sistema.') }}</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
        <div class="mb-4 font-medium text-sm" style="color: oklch(43% 0.095 166.913);">
            {{ $value }}
        </div>
        @endsession

        <livewire:social.buttons />
        <div class="divider my-4" style="color: oklch(65% 0.06 38.172);">ou</div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium mb-1" style="color: oklch(40% 0.123 38.172);">
                    {{ __('Email') }}
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       class="w-full rounded-lg px-4 py-3 text-base outline-none transition focus:ring-2"
                       style="background-color: oklch(95% 0.038 75.164);
                              color: oklch(30% 0.08 38.172);
                              border: 1.5px solid oklch(88% 0.06 70.697);
                              focus-ring-color: oklch(46.44% 0.111 37.85);" />
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium mb-1" style="color: oklch(40% 0.123 38.172);">
                    {{ __('Password') }}
                </label>
                <input id="password"
                       type="password"
                       name="password"
                       required autocomplete="current-password"
                       class="w-full rounded-lg px-4 py-3 text-base outline-none transition focus:ring-2"
                       style="background-color: oklch(95% 0.038 75.164);
                              color: oklch(30% 0.08 38.172);
                              border: 1.5px solid oklch(88% 0.06 70.697);" />
            </div>

            {{-- Remember + Forgot --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="rounded"
                           style="accent-color: oklch(22.45% 0.075 37.85); width: 1rem; height: 1rem;" />
                    <span class="text-sm" style="color: oklch(45% 0.08 38.172);">{{ __('Lembrar-me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm underline transition"
                       style="color: oklch(46.44% 0.111 37.85);"
                       onmouseover="this.style.color='oklch(22.45% 0.075 37.85)'"
                       onmouseout="this.style.color='oklch(46.44% 0.111 37.85)'">
                        {{ __('Esqueceste a password?') }}
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3 rounded-lg text-base font-semibold transition hover:opacity-90 mt-2"
                    style="background-color: oklch(22.45% 0.075 37.85);
                           color: oklch(90% 0.076 70.697);">
                {{ __('Entrar') }}
            </button>

        </form>
    </div>
</x-guest-layout>
