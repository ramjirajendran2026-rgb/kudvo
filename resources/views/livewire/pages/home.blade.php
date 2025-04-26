<main
    class="page-home min-h-screen w-full overflow-hidden bg-gradient-to-b from-gray-50 to-blue-50 font-sans"
>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.65);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .neumorph {
            box-shadow:
                8px 8px 24px #e3e8f0,
                -8px -8px 24px #fff;
        }
        .btn-primary {
            background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);
            color: #fff;
            border-radius: 9999px;
            box-shadow: 0 2px 8px 0 #2563eb33;
            font-weight: 600;
            transition:
                background 0.2s,
                box-shadow 0.2s,
                transform 0.1s;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background: #1e40af;
            transform: scale(1.04);
            box-shadow: 0 4px 16px 0 #2563eb44;
        }
        .focus-outline:focus {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }
    </style>
    <section
        id="hero"
        class="container relative overflow-hidden !px-0"
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
                }, 5000)
            },

            prevSlide() {
                this.activateSlide(this.activeSlide - 1 + this.slides)
            },

            nextSlide() {
                this.activateSlide(this.activeSlide + 1)
            },

            activateSlide(slide) {
                if (this.intervalId !== null) {
                    clearInterval(this.intervalId)
                }

                this.activeSlide = slide % this.slides
                this.intervalId = setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slides
                }, 5000)
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
                class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0"
                tabindex="0"
                aria-live="polite"
            >
                <!-- Large screens: image as background with overlay card -->
                <div
                    class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]"
                >
                    <img
                        {!! ! $loop->first ? 'loading="lazy"' : '' !!}
                        class="animated-image absolute inset-0 h-full w-full rounded-2xl object-cover object-center"
                        src="{{ $item->image }}"
                        alt="{{ $item->image_alt }}"
                        title="{{ $item->title }}"
                        x-bind:class="{ 'animated-image': activeSlide === currentSlide && started }"
                        style="filter: brightness(0.97)"
                    />
                    <div
                        class="glass neumorph fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16"
                        x-bind:class="{ 'animated-image': activeSlide === currentSlide }"
                    >
                        <h2
                            class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl"
                        >
                            {{ $item->title }}
                        </h2>
                        <ul
                            class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl"
                        >
                            <li>{{ $item->description }}</li>
                        </ul>
                        <div
                            class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5"
                        >
                            @if (filled($item->cta_label))
                                <a
                                    href="{{ $item->cta_url }}"
                                    class="btn-primary focus-outline xs:text-sm px-5 py-2 text-center text-xs sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg"
                                    tabindex="0"
                                    role="button"
                                >
                                    {{ $item->cta_label }}
                                </a>
                            @endif

                            @if (filled($item->cta2_label))
                                <a
                                    href="{{ $item->cta2_url }}"
                                    class="btn-primary focus-outline xs:text-sm bg-green-500 px-5 py-2 text-center text-xs hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg"
                                    tabindex="0"
                                    role="button"
                                >
                                    {{ $item->cta2_label }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div
                        class="glass neumorph fade-in px-3 py-5 shadow-lg sm:px-6 sm:py-6"
                    >
                        <h2
                            class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl"
                        >
                            {{ $item->title }}
                        </h2>
                        <ul
                            class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg"
                        >
                            <li>{{ $item->description }}</li>
                        </ul>
                        <div
                            class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center"
                        >
                            @if (filled($item->cta_label))
                                <a
                                    href="{{ $item->cta_url }}"
                                    class="btn-primary focus-outline xs:text-sm px-5 py-2 text-center text-xs sm:px-6 sm:py-3 sm:text-base"
                                    tabindex="0"
                                    role="button"
                                >
                                    {{ $item->cta_label }}
                                </a>
                            @endif

                            @if (filled($item->cta2_label))
                                <a
                                    href="{{ $item->cta2_url }}"
                                    class="btn-primary focus-outline xs:text-sm bg-green-500 px-5 py-2 text-center text-xs hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base"
                                    tabindex="0"
                                    role="button"
                                >
                                    {{ $item->cta2_label }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <img
                        {!! ! $loop->first ? 'loading="lazy"' : '' !!}
                        class="animated-image mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64"
                        src="{{ $item->image }}"
                        alt="{{ $item->image_alt }}"
                        title="{{ $item->title }}"
                        x-bind:class="{ 'animated-image': activeSlide === currentSlide && started }"
                        style="filter: brightness(0.97)"
                    />
                </div>
            </div>
        @endforeach

        <div class="flex justify-center">
            <template x-for="slide in slides" :key="slide">
                <button
                    @click="activateSlide(slide - 1)"
                    :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }"
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
            @lang('pages/home.content.headline')
        </h1>
    </section>

    <section id="features" class="container pt-16">
        <h2 class="text-center text-2xl font-semibold text-black md:text-4xl">
            {{ __('pages/home.content.features.title') }}
        </h2>
        <div class="mt-5 grid gap-4 sm:grid-cols-2 md:grid-cols-3 md:gap-8">
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

    <section id="contact" class="h-20 rounded-t-[100%] bg-white"></section>

    <section class="!mt-0 bg-white pb-16">
        <div
            class="container flex flex-col items-center justify-between gap-6 md:flex-row md:gap-12"
        >
            <div class="w-full flex-1 space-y-6">
                <h3
                    class="text-center text-2xl font-semibold md:text-start md:text-4xl"
                >
                    {{ __('pages/home.content.contact.title') }}
                </h3>

                <livewire:contact-form lazy="on-load" />
            </div>

            <div
                class="relative flex aspect-video w-full flex-1 items-center justify-center"
            >
                <img
                    loading="lazy"
                    src="{{ url('img/contact-bg.webp') }}"
                    alt="An abstract geometric pattern with repeating blue and light purple shapes, including circles, semi-circles, and triangles, creating a mosaic-like design."
                    title="Contact Us"
                    class="absolute inset-0 h-full w-full object-cover md:rounded-3xl"
                />
                <div class="relative space-y-4 rounded-xl bg-white p-4 md:p-6">
                    <h4 class="text-center text-2xl font-semibold md:text-4xl">
                        {{ config('app.name') }}
                    </h4>
                    <div class="flex items-center justify-center gap-4">
                        <x-filament::icon
                            icon="heroicon-o-phone"
                            class="h-6 w-6"
                        />
                        <div class="text-lg">
                            <span class="hidden sm:inline">
                                {{ __('app.contact.phone.label') }}
                            </span>
                            <a
                                href="tel:{{ __('app.contact.phone.number') }}"
                                class="cursor-pointer text-nowrap hover:text-primary-700 hover:underline"
                            >
                                {{ __('app.contact.phone.number') }}
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-4">
                        <x-filament::icon
                            icon="heroicon-o-envelope"
                            class="h-6 w-6"
                        />
                        <a
                            href="mailto:{{ __('app.contact.email.address') }}"
                            class="cursor-pointer text-lg hover:text-primary-700 hover:underline"
                        >
                            {{ __('app.contact.email.address') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
