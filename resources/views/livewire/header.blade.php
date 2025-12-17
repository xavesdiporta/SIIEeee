<div>
    <div class="max-w-7xl m-auto navbar fixed top-4 left-0 right-0 z-50 backdrop-blur-xl shadow-lg border border-[oklch(90%_0.076_70.697_/0.4)] rounded-3xl transition-all duration-300" style="background-color: oklch(98% 0.016 73.684 / 0.65); color: oklch(40% 0.123 38.172);margin-inline: auto;">

        <!-- Navbar start -->
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button"
                     class="btn btn-ghost lg:hidden"
                     style="color: oklch(40% 0.123 38.172);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow rounded-lg w-52 backdrop-blur-xl border border-[oklch(90%_0.076_70.697_/0.4)]"  style=" background-color: oklch(98% 0.016 73.684 / 0.85); color: oklch(40% 0.123 38.172);">
                    <li><a href="#">{{ __('About Us') }}</a></li>
                    <li><a href="#">{{ __('Pricing') }}</a></li>
                    <li><a href="#">{{ __('How It Works') }}</a></li>
                    <li><a href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
                </ul>
            </div>

            <a href="/" class="flex flex-row items-center justify-center font-bold text-md">
                <img class="w-16" src="{{ asset('/images/logo.svg') }}" alt="">
                <span class="ml-2 flex flex-col items-start">
                    <span class="leading-4" style="color: oklch(22.45% 0.075 37.85);">
                        {{ __('Your Startup Name') }}
                    </span>
                </span>
            </a>
        </div>

        <!-- Navbar center -->
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1" style="color: oklch(40% 0.123 38.172);">
                <li><a href="#">{{ __('About Us') }}</a></li>
                <li><a href="#">{{ __('Pricing') }}</a></li>
                <li><a href="#">{{ __('How It Works') }}</a></li>
                <li><a href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
                <li><a href="{{ route('coming-soon') }}">{{ __('Coming Soon') }}</a></li>
            </ul>
        </div>

        <!-- Navbar end -->
        <div class="navbar-end">
            <a href="{{ route('login') }}" class="btn btn-widest" style="background-color: oklch(22.45% 0.075 37.85); color: oklch(90% 0.076 70.697); border: 2px solid oklch(22.45% 0.075 37.85); transition: all 0.3s ease; border-radius: 1rem;"  onmouseover="this.style.backgroundColor='oklch(46.44% 0.111 37.85)'" onmouseout="this.style.backgroundColor='oklch(22.45% 0.075 37.85)'">
                {{ __('Get Started') }}
            </a>
        </div>
    </div>

    <!-- Espaçamento superior -->
    <div style="height: 100px;"></div>
</div>
