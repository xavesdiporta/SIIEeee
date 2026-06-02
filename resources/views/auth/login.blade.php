<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex flex-col items-center mb-2">
                <img src="/images/logo-banana.jpg" alt="Agrupamento 542" class="w-24 h-24 rounded-full object-contain shadow-md" />
                <p class="mt-3 text-lg font-semibold" style="color: oklch(40% 0.123 38.172);">
                    {{ __('Agrupamento 542 · Entroncamento') }}
                </p>
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
        <div class="mb-4 font-medium text-sm" style="color: oklch(43% 0.095 166.913);">
            {{ $value }}
        </div>
        @endsession

        <livewire:social.buttons />
        <div class="divider" style="color: oklch(40% 0.123 38.172);">or</div>

        <form method="POST" action="{{ route('login') }}"
              style="background-color: oklch(98% 0.016 73.684);
                     color: oklch(40% 0.123 38.172);
                     border-radius: 1rem;
                     padding: 2.5rem;">
            @csrf

            <!-- Email -->
            <div>
                <x-label for="email" value="{{ __('Email') }}"
                         class="text-base"
                         style="color: oklch(40% 0.123 38.172);" />
                <x-input id="email"
                         class="block mt-1 w-full text-base py-3 px-4"
                         type="email"
                         name="email"
                         :value="old('email')"
                         required autofocus autocomplete="username"
                         style="background-color: oklch(95% 0.038 75.164);
                                color: oklch(40% 0.123 38.172);
                                border: 1px solid oklch(90% 0.076 70.697);
                                border-radius: 0.5rem;" />
            </div>

            <!-- Password -->
            <div class="mt-5">
                <x-label for="password" value="{{ __('Password') }}"
                         class="text-base"
                         style="color: oklch(40% 0.123 38.172);" />
                <x-input id="password"
                         class="block mt-1 w-full text-base py-3 px-4"
                         type="password"
                         name="password"
                         required autocomplete="current-password"
                         style="background-color: oklch(95% 0.038 75.164);
                                color: oklch(40% 0.123 38.172);
                                border: 1px solid oklch(90% 0.076 70.697);
                                border-radius: 0.5rem;" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-5">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember"
                                style="accent-color: oklch(22.45% 0.075 37.85); width: 1.1rem; height: 1.1rem;" />
                    <span class="ms-2 text-base" style="color: oklch(40% 0.123 38.172);">
                        {{ __('Remember me') }}
                    </span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                    <a class="underline text-sm rounded-md focus:outline-none"
                       href="{{ route('password.request') }}"
                       style="color: oklch(46.44% 0.111 37.85); transition: color 0.3s;"
                       onmouseover="this.style.color='oklch(22.45% 0.075 37.85)'"
                       onmouseout="this.style.color='oklch(46.44% 0.111 37.85)'">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button style="background-color: oklch(22.45% 0.075 37.85);
                                 color: oklch(90% 0.076 70.697);
                                 border: 2px solid oklch(22.45% 0.075 37.85);
                                 border-radius: 0.5rem;
                                 padding: 0.65rem 1.75rem;
                                 font-size: 1rem;">
                    {{ __('Log in') }}
                </x-button>
            </div>

            <!-- Divider -->
            <div class="mt-6 border-t" style="border-color: oklch(88% 0.06 70.697);"></div>

            <!-- Register -->
            <div class="flex flex-col items-center mt-5">
                <p class="text-base" style="color: oklch(40% 0.123 38.172);">
                    {{ __('Do not have an account?') }}
                </p>
                <a href="{{ route('register') }}"
                   class="mt-3 w-full text-center py-3 rounded-lg text-base font-medium transition-opacity hover:opacity-90"
                   style="background-color: oklch(46.44% 0.111 37.85);
                          color: oklch(90% 0.076 70.697);
                          border: 2px solid oklch(46.44% 0.111 37.85);
                          border-radius: 0.5rem;">
                    {{ __('Register') }}
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
