<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0"
     style="background-color: oklch(98% 0.016 73.684); color: oklch(40% 0.123 38.172);">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg"
         style="
            background-color: oklch(95% 0.038 75.164);
            color: oklch(40% 0.123 38.172);
            box-shadow: 0 4px 12px oklch(55% 0.195 38.402 / 0.2);
            border: 1px solid oklch(90% 0.076 70.697);
         ">
        {{ $slot }}
    </div>
</div>
