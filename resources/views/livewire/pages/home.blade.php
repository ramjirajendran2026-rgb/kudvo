<main class="page-home h-full w-full">
    <section
        id="hero"
        class="container relative overflow-hidden"
        x-data="{
            started: false,
            activeSlide: 0,
            slides: @js($heroItems->count()),
            intervalId: null,
            slideItems: @js($heroItems->toArray()),

            startSlider() {
                this.intervalId = setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slides

                    if (! this.started) {
                        this.started = true
                    }
                }, 7000)
            },

            prevSlide() {
                clearInterval(this.intervalId)
                this.activeSlide = (this.activeSlide - 1 + this.slides) % this.slides
                this.intervalId = setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slides
                }, 7000)
            },

            nextSlide() {
                clearInterval(this.intervalId)
                this.activeSlide = (this.activeSlide + 1) % this.slides
                this.intervalId = setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slides
                }, 7000)
            },
        }"
        x-init="startSlider()"
    >
        <button
            aria-label="Previous slide"
            @click="prevSlide()"
            class="absolute left-5 top-1/2 z-10 -translate-y-1/2 transform rounded-full bg-gray-200 p-2 opacity-10 hover:opacity-100 focus:outline-none"
        >
            <svg
                class="h-6 w-6 text-gray-600"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button
            aria-label="Next slide"
            @click="nextSlide()"
            class="absolute right-5 top-1/2 z-10 -translate-y-1/2 transform rounded-full bg-gray-200 p-2 opacity-10 hover:opacity-100 focus:outline-none"
        >
            <svg
                class="h-6 w-6 text-gray-600"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        @foreach ($heroItems as $item)
            <div
                {{ ! $loop->first ? 'x-cloak' : '' }}
                x-data="{ currentSlide: @js($loop->index) }"
                x-show="activeSlide === currentSlide"
                class="relative flex flex-col bg-cover md:aspect-[2.23/1] md:bg-var-url lg:flex-row lg:pb-0 lg:pt-0"
            >
                <img
                    {!! ! $loop->first ? 'loading="lazy"' : '' !!}
                    class="absolute inset-0 -z-10 hidden aspect-[2.23/1] object-cover md:block"
                    src="{{ $item->image }}"
                    alt="{{ $item->title }}"
                    title="{{ $item->title }}"
                    x-bind:class="{
                        'animated-image': activeSlide === currentSlide && started,
                    }"
                />
                <div
                    class="absolute inset-0 hidden bg-gradient-to-r from-gray-50 from-40% md:block"
                ></div>
                <div
                    class="relative flex h-full w-full flex-col items-start justify-center px-4 md:w-[66.6%] md:px-0 lg:w-1/2 lg:px-8"
                    x-bind:class="{
                        'animated-image': activeSlide === currentSlide && started,
                    }"
                >
                    <div class="mb-16 md:mb-0 lg:my-auto lg:pr-5">
                        <h2
                            class="mb-5 font-sans text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl sm:leading-none"
                        >
                            {{ $item->title }}
                        </h2>

                        <ul
                            class="mb-5 pr-5 text-base text-gray-700 md:text-lg"
                        >
                            <li>{{ $item->description }}</li>
                        </ul>

                        <div class="flex items-center gap-4">
                            @if (filled($item->cta_label))
                                <x-filament::button
                                    size="xl"
                                    color="primary"
                                    tag="a"
                                    :href="$item->cta_url ?? '#'"
                                >
                                    {{ $item->cta_label }}
                                </x-filament::button>
                            @endif

                            @if (filled($item->cta2_label))
                                <x-filament::link
                                    size="xl"
                                    color="primary"
                                    tag="a"
                                    :href="$item->cta2_url ?? '#'"
                                >
                                    {{ $item->cta2_label }}
                                </x-filament::link>
                            @endif
                        </div>
                    </div>
                </div>
                <div
                    class="inset-y-0 right-0 top-0 z-0 mx-auto block w-full md:hidden md:px-0 lg:absolute lg:mx-0 lg:mb-0 lg:pr-0 xl:px-0"
                >
                    <img
                        {!! ! $loop->first ? 'loading="lazy"' : '' !!}
                        class="block h-[343px] w-full object-cover object-right-bottom [mask-image:_linear-gradient(to_bottom,transparent_0,_black_100px,_black_calc(100%-1px),transparent_100%)] md:hidden"
                        src="{{ $item->image }}"
                        alt="{{ $item->title }}"
                        title="{{ $item->title }}"
                        x-bind:class="{
                            'animated-image': activeSlide === currentSlide && started,
                        }"
                    />
                </div>
            </div>
        @endforeach

        <div class="absolute bottom-0 left-0 right-0 mb-8 flex justify-center">
            <template x-for="slide in slides" :key="slide">
                <button
                    @click="activeSlide = slide - 1"
                    :class="{ 'bg-primary-500': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }"
                    class="mx-1 h-3 w-6 rounded-full focus:outline-none"
                    x-transition:enter="transition-all duration-300 ease-out"
                    x-transition:enter-start="scale-95 opacity-0"
                    x-transition:enter-end="scale-100 opacity-100"
                    x-transition:leave="transition-all duration-200 ease-out"
                    x-transition:leave-start="scale-100 opacity-100"
                    x-transition:leave-end="scale-95 opacity-0"
                >
                    <span
                        x-text="'Slider '+slide+' selector'"
                        class="sr-only"
                    ></span>
                </button>
            </template>
        </div>
    </section>

    <section id="intro" class="container pt-16">
        <h1
            class="mx-auto max-w-screen-lg text-center text-xl font-bold text-gray-900 sm:text-2xl md:text-4xl"
        >
            Secure
            <span
                class="bg-gradient-to-tr from-primary-600 to-primary-500 bg-clip-text text-transparent"
            >
                Online Voting System
            </span>
            for Seamless Election Management Anytime, Anywhere
        </h1>
    </section>

    <section id="features" class="container pt-16">
        <h2
            class="underline-offset-3 text-center text-2xl font-semibold text-black underline decoration-primary-600 decoration-8 md:text-4xl"
        >
            Explore Our Features
        </h2>
        <div class="grid gap-4 md:grid-cols-3 md:gap-8">
            @foreach ($featureItems as $feature)
                <x-home.feature-card :data="$feature" />
            @endforeach
        </div>
    </section>

    <section id="products" class="container pt-16">
        <div
            class="space-y-6 rounded-3xl bg-primary-700/30 p-6 md:space-y-8 md:p-8"
        >
            <h4
                class="text-center text-2xl font-semibold text-primary-950 md:text-4xl"
            >
                {{ __('pages/home.content.products.title') }}
            </h4>
            <div class="grid gap-4 md:grid-cols-3 md:gap-8">
                @foreach ($productItems as $product)
                    <x-home.product-card :data="$product" />
                @endforeach
            </div>
        </div>
    </section>

    <section id="clientele" class="container space-y-6 py-16">
        <h4 class="text-center text-2xl font-semibold md:text-4xl">
            {{ __('pages/home.content.clientele.title') }}
        </h4>
        <div
            x-data="{}"
            x-init="
                $nextTick(() => {
                    let ul = $refs.items
                    ul.insertAdjacentHTML('afterend', ul.outerHTML)
                    ul.nextSibling.setAttribute('aria-hidden', 'true')
                })
            "
            class="my-4 inline-flex w-full flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)]"
        >
            <ul
                x-ref="items"
                class="flex animate-infinite-scroll items-center justify-center md:justify-start"
            >
                @foreach ($clientItems as $client)
                    <li class="mx-2 md:mx-8">
                        <img
                            loading="lazy"
                            class="aspect-square w-16 max-w-none md:w-28"
                            src="{{ $client->logo }}"
                            alt="{{ $client->name }}"
                            title="{{ $client->name }}"
                        />
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="h-20 rounded-t-[100%] bg-white"></section>

    <section id="contact" class="!mt-0 bg-white pb-16">
        <div
            class="container flex flex-col items-center justify-between gap-6 md:flex-row md:gap-12"
        >
            <div class="w-full flex-1 space-y-6">
                <h3
                    class="text-center text-2xl font-semibold md:text-start md:text-4xl"
                >
                    {{ __('pages/home.content.contact.title') }}
                </h3>

                <livewire:contact-form />
            </div>

            <div
                class="flex aspect-video w-full flex-1 items-center justify-center bg-cover bg-no-repeat p-4 md:rounded-3xl"
                style="background-image: url({{ url('img/contact-bg.png') }})"
            >
                <div class="space-y-4 rounded-xl bg-white p-4 md:p-6">
                    <h4 class="text-center text-2xl font-semibold md:text-4xl">
                        {{ config('app.name') }}
                    </h4>
                    <div class="flex items-center gap-4">
                        <x-filament::icon
                            icon="heroicon-o-phone"
                            class="h-6 w-6"
                        />
                        <div class="text-lg">
                            Call / Whatsapp
                            <a
                                href="tel:+1-631-731-3526"
                                class="cursor-pointer text-nowrap"
                            >
                                +1-631-731-3526
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <x-filament::icon
                            icon="heroicon-o-envelope"
                            class="h-6 w-6"
                        />
                        <a
                            href="mailto://support@kudvo.com"
                            class="cursor-pointer text-lg"
                        >
                            support@kudvo.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
