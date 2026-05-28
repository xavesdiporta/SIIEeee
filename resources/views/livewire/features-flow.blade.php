<section class="relative py-8 lg:py-20" id="features">
    <div class="absolute start-[10%] z-0">
        <div
            class="pointer-events-none aspect-square w-60 rounded-full bg-gradient-to-r from-primary/10 via-violet-500/10 to-purple-500/10 blur-3xl [transform:translate3d(0,0,0)] lg:w-[600px]"
        ></div>
    </div>

    <div class="container">
        <div class="flex flex-col items-center">
            <h2 class="inline text-4xl font-semibold">{{ __('As Secções do Agrupamento') }}</h2>
            <p class="mt-4 text-lg sm:text-center">
                {{ __('O Agrupamento 542 está organizado em secções por faixas etárias, cada uma com o seu método e espírito próprios.') }}
            </p>
        </div>

        {{-- Lobitos --}}
        <div class="relative z-[2] mt-8 grid gap-8 lg:mt-20 lg:grid-cols-2 lg:gap-12">
            <div class="overflow-hidden rounded-lg bg-base-200 shadow-md transition-all hover:shadow-xl">
                {{-- Substitui pelo caminho da tua foto dos Lobitos --}}
                <img alt="Lobitos" class="overflow-hidden rounded-ss-lg w-full object-cover" src="/images/seccoes/lobitos.jpg" />
            </div>
            <div class="lg:mt-8">
                <div class="badge badge-warning text-white">{{ __('6 – 10 anos') }}</div>
                <h3 class="mt-2 text-3xl font-semibold">{{ __('Lobitos') }}</h3>
                <p class="mt-2 text-base font-medium">
                    {{ __('Os Lobitos são a secção mais jovem do agrupamento. Inspirados no Livro da Selva, desenvolvem a imaginação, a amizade e os primeiros valores escutistas.') }}
                </p>
                <ul class="mt-4 list-inside list-disc text-base">
                    <li>{{ __('Jogos e atividades ao ar livre') }}</li>
                    <li>{{ __('Primeiros valores escutistas') }}</li>
                    <li>{{ __('Espírito de matilha') }}</li>
                    <li>{{ __('Criatividade e imaginação') }}</li>
                </ul>
            </div>
        </div>

        {{-- Escuteiros --}}
        <div class="mt-8 grid gap-8 lg:mt-20 lg:grid-cols-2 lg:gap-12">
            <div>
                <div class="badge badge-success text-white">{{ __('11 – 14 anos') }}</div>
                <h3 class="mt-2 text-3xl font-semibold">{{ __('Escuteiros') }}</h3>
                <p class="mt-2 text-base">
                    {{ __('A secção dos Escuteiros é onde o método escutista se desenvolve plenamente. Acampamentos, trilhos, técnicas de campo e serviço à comunidade são o coração desta secção.') }}
                </p>
                <ul class="mt-4 list-inside list-disc text-base">
                    <li>{{ __('Acampamentos e trilhos') }}</li>
                    <li>{{ __('Técnicas de campo e orientação') }}</li>
                    <li>{{ __('Serviço à comunidade') }}</li>
                    <li>{{ __('Sistema de progressão pessoal') }}</li>
                </ul>
            </div>
            <div class="order-first lg:order-last">
                <div class="overflow-hidden rounded-lg bg-base-200 shadow-md transition-all hover:shadow-xl">
                    {{-- Substitui pelo caminho da tua foto dos Escuteiros --}}
                    <img alt="Escuteiros" class="overflow-hidden rounded-ss-lg w-full object-cover" src="/images/seccoes/escuteiros.jpg" />
                </div>
            </div>
        </div>

        {{-- Pioneiros --}}
        <div class="mt-8 grid gap-8 lg:mt-20 lg:grid-cols-2 lg:gap-12">
            <div class="overflow-hidden rounded-lg bg-base-200 shadow-md transition-all hover:shadow-xl">
                {{-- Substitui pelo caminho da tua foto dos Pioneiros --}}
                <img alt="Pioneiros" class="overflow-hidden rounded-ss-lg w-full object-cover" src="/images/seccoes/pioneiros.jpg" />
            </div>
            <div class="lg:mt-8">
                <div class="badge badge-error text-white">{{ __('15 – 17 anos') }}</div>
                <h3 class="mt-2 text-3xl font-semibold">{{ __('Pioneiros') }}</h3>
                <p class="mt-2 text-base">
                    {{ __('Os Pioneiros assumem maior autonomia e responsabilidade. Desenvolvem projetos de serviço, participam em eventos nacionais e internacionais e aprofundam o seu crescimento pessoal.') }}
                </p>
                <ul class="mt-4 list-inside list-disc text-base">
                    <li>{{ __('Projetos de serviço à comunidade') }}</li>
                    <li>{{ __('Eventos nacionais e internacionais') }}</li>
                    <li>{{ __('Autonomia e liderança') }}</li>
                    <li>{{ __('Desenvolvimento pessoal') }}</li>
                </ul>
            </div>
        </div>

        {{-- Caminheiros --}}
        <div class="mt-8 grid gap-8 lg:mt-20 lg:grid-cols-2 lg:gap-12">
            <div>
                <div class="badge badge-info text-white">{{ __('18 – 21 anos') }}</div>
                <h3 class="mt-2 text-3xl font-semibold">{{ __('Caminheiros') }}</h3>
                <p class="mt-2 text-base">
                    {{ __('Os Caminheiros são a secção mais sénior. Num espírito de aventura e partilha, preparam a transição para a vida adulta com valores sólidos e experiências marcantes.') }}
                </p>
                <ul class="mt-4 list-inside list-disc text-base">
                    <li>{{ __('Aventura e exploração') }}</li>
                    <li>{{ __('Responsabilidade e cidadania') }}</li>
                    <li>{{ __('Projetos de impacto social') }}</li>
                    <li>{{ __('Transição para a vida adulta') }}</li>
                </ul>
            </div>
            <div class="order-first lg:order-last">
                <div class="overflow-hidden rounded-lg bg-base-200 shadow-md transition-all hover:shadow-xl">
                    {{-- Substitui pelo caminho da tua foto dos Caminheiros --}}
                    <img alt="Caminheiros" class="overflow-hidden rounded-ss-lg w-full object-cover" src="/images/cla.jpeg" />
                </div>
            </div>
        </div>

    </div>
</section>
