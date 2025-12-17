<footer class="footer p-10"
        style="background-color: oklch(95% 0.038 75.164);
               color: oklch(40% 0.123 38.172);">

    <nav>
        <h6 class="footer-title" style="color: oklch(22.45% 0.075 37.85);">
            {{ __('Services') }}
        </h6>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Branding') }}</a>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Design') }}</a>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Marketing') }}</a>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Advertisement') }}</a>
    </nav>

    <nav>
        <h6 class="footer-title" style="color: oklch(22.45% 0.075 37.85);">
            {{ __('Company') }}
        </h6>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('About us') }}</a>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Contact') }}</a>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Jobs') }}</a>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Press kit') }}</a>
    </nav>

    <nav>
        <h6 class="footer-title" style="color: oklch(22.45% 0.075 37.85);">
            {{ __('Legal') }}
        </h6>
        <a href="{{ route('terms.show') }}" class="link link-hover"
           style="color: oklch(40% 0.123 38.172);">{{ __('Terms of use') }}</a>
        <a href="{{ route('policy.show') }}" class="link link-hover"
           style="color: oklch(40% 0.123 38.172);">{{ __('Privacy policy') }}</a>
        <a class="link link-hover" style="color: oklch(40% 0.123 38.172);">{{ __('Cookie policy') }}</a>
    </nav>

    <form>
        <h6 class="footer-title" style="color: oklch(22.45% 0.075 37.85);">
            {{ __('Newsletter') }}
        </h6>
        <fieldset class="form-control w-80">
            <label class="label">
                <span class="label-text" style="color: oklch(40% 0.123 38.172);">
                    {{ __('Enter your email address') }}
                </span>
            </label>
            <div class="join">
                <input type="text"
                       placeholder="{{ __('username@site.com') }}"
                       class="input input-bordered join-item"
                       style="background-color: oklch(98% 0.016 73.684);
                              color: oklch(40% 0.123 38.172);
                              border: 1px solid oklch(90% 0.076 70.697);" />
                <button class="btn join-item"
                        style="background-color: oklch(22.45% 0.075 37.85);
                               color: oklch(90% 0.076 70.697);
                               border: 2px solid oklch(22.45% 0.075 37.85);">
                    {{ __('Subscribe') }}
                </button>
            </div>
        </fieldset>
    </form>
</footer>
