<div>
    <div class="bg-gradient-to-b from-gray-50 to-primary-100">
        <section class="py-5 sm:py-8 lg:py-6">
            <div class="mx-auto h-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 items-center gap-12 px-3 md:px-10 lg:grid-cols-2"
                >
                    <div>
                        <h1
                            data-aos="fade-right"
                            data-aos-easing="ease-out-cubic"
                            data-aos-delay="10"
                            class="text-3xl font-bold text-black sm:text-4xl"
                        >
                            {{ __('pages/products/meeting/home.content.hero.title') }}
                            <div class="relative inline-flex">
                                <span
                                    class="absolute inset-x-0 bottom-0 border-b-[33px] border-primary-100 sm:border-b-[37px]"
                                ></span>
                                <h1
                                    data-aos="flip-up"
                                    data-aos-easing="ease-in-back"
                                    data-aos-delay="100"
                                    class="relative text-3xl font-bold text-primary-800 sm:text-4xl"
                                >
                                    {{ __('pages/products/meeting/home.content.hero.highlight') }}
                                </h1>
                            </div>
                        </h1>

                        <p
                            data-aos="fade-right"
                            data-aos-easing="ease-out-cubic"
                            data-aos-delay="50"
                            class="mt-8 text-base text-black sm:text-lg"
                        >
                            {{ __('pages/products/meeting/home.content.hero.description') }}
                        </p>

                        <div
                            data-aos="fade-right"
                            data-aos-easing="ease-out-cubic"
                            data-aos-delay="100"
                            class="mt-6 sm:flex sm:items-center sm:space-x-8"
                        >
                            <x-filament::button
                                size="xl"
                                color="primary"
                                tag="a"
                                :href="__('pages/products/meeting/home.content.hero.cta.url')"
                            >
                                {{ __('pages/products/meeting/home.content.hero.cta.label') }}
                            </x-filament::button>
                        </div>
                    </div>

                    <div>
                        <img
                            loading="lazy"
                            data-aos="fade-up"
                            class="mx-auto w-full md:size-10/12"
                            src="{{ __('pages/products/meeting/home.content.hero.image') }}"
                            alt="{{ __('pages/products/meeting/home.content.hero.image_alt') }}"
                            title="{{ __('pages/products/meeting/home.content.hero.title') }}
                             {{ __('pages/products/meeting/home.content.hero.highlight') }}"
                        />
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="bg-gray-50 py-10 sm:py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-xl text-center">
                <p
                    class="text-sm font-semibold uppercase tracking-widest text-primary-600"
                >
                    {{ __('pages/products/meeting/home.content.key_features.label') }}
                </p>

                <h2
                    class="mt-6 text-2xl font-bold leading-tight text-black sm:text-3xl"
                >
                    {{ __('pages/products/meeting/home.content.key_features.title') }}
                </h2>
            </div>

            <div
                id="example-anchor"
                class="mt-12 grid grid-cols-1 items-center gap-x-4 gap-y-10 sm:mt-20 lg:grid-cols-5"
            >
                <div
                    class="space-y-8 lg:col-span-2 lg:space-y-12 lg:pr-16 xl:pr-24"
                >
                    @php($delay = -100)
                    @foreach (__('pages/products/meeting/home.content.key_features.items') as $item)
                        @php($delay += 200)
                        <div
                            data-aos="fade-up"
                            data-aos-anchor="#example-anchor"
                            data-aos-easing="ease-out-cubic"
                            data-aos-delay="{{ $delay }}"
                            class="flex items-start"
                        >
                            <x-filament::icon
                                :icon="$item['icon']"
                                :color="$item['color']"
                                class="h-9 w-9 flex-shrink-0"
                            />
                            <div class="ml-5">
                                <h3 class="text-xl font-semibold text-black">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="mt-3 text-base text-gray-600">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="lg:col-span-3">
                    <img
                        loading="lazy"
                        data-aos="zoom-in"
                        data-aos-easing="ease-out-cubic"
                        class="w-full rounded-lg shadow-xl"
                        src="{{ __('pages/products/meeting/home.content.key_features.image') }}"
                        title="{{ __('pages/products/meeting/home.content.key_features.title') }}"
                        alt="{{ __('pages/products/meeting/home.content.key_features.image_alt') }}"
                    />
                </div>
            </div>
        </div>
    </section>

    <section
        style="
            background: url({{ asset('img/products/meeting/dot-bg.webp') }});
            background-repeat: no-repeat;
            background-size: cover;
        "
        class="py-10 sm:py-16 lg:py-24"
    >
        <div id="num" class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2
                    class="text-2xl font-bold leading-tight text-black sm:text-3xl"
                >
                    {{ __('pages/products/meeting/home.content.numbers.title') }}
                </h2>
                <p class="mt-3 text-xl leading-relaxed text-gray-600 md:mt-8">
                    {{ __('pages/products/meeting/home.content.numbers.description') }}
                </p>
            </div>

            <div
                class="mt-10 grid grid-cols-1 gap-8 text-center sm:gap-x-8 md:grid-cols-3 lg:mt-24"
            >
                @php($delay = -100)
                @foreach (__('pages/products/meeting/home.content.numbers.items') as $item)
                    @php($delay += 200)
                    <div>
                        <h3
                            data-aos="zoom-in"
                            data-aos-anchor="#num"
                            data-aos-easing="ease-out-cubic"
                            data-aos-delay="{{ $delay }}"
                            class="text-5xl font-bold"
                        >
                            <span
                                class="bg-gradient-to-r from-fuchsia-600 to-blue-600 bg-clip-text text-transparent"
                            >
                                {{ $item['number'] }}
                            </span>
                        </h3>
                        <p class="mt-4 text-xl font-medium text-gray-900">
                            {{ $item['title'] }}
                        </p>
                        <p class="mt-0.5 text-base text-gray-500">
                            {{ $item['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-10 sm:py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-1 items-center gap-y-12 lg:grid-cols-2 lg:gap-x-24"
            >
                <div class="text-center lg:text-left">
                    <h2
                        class="text-2xl font-bold leading-tight text-black sm:text-3xl"
                    >
                        {{ __('pages/products/meeting/home.content.industries.title') }}
                    </h2>
                    <p class="mt-6 text-base text-gray-600">
                        {{ __('pages/products/meeting/home.content.industries.description') }}
                    </p>
                    <div
                        class="flex w-full flex-col items-center justify-center lg:pl-8"
                    >
                        <ul
                            class="mt-6 space-y-2 text-justify text-base text-gray-600 lg:list-disc"
                        >
                            @foreach (__('pages/products/meeting/home.content.industries.items') as $item)
                                <li>
                                    <b>{{ $item['title'] }}</b>
                                    {{ $item['description'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div>
                    <img
                        loading="lazy"
                        data-aos="zoom-in"
                        data-aos-duration="1000"
                        data-aos-easing="ease-out-cubic"
                        class="mx-auto w-full max-w-md"
                        title="{{ __('pages/products/meeting/home.content.industries.title') }}"
                        alt="{{ __('pages/products/meeting/home.content.industries.image_alt') }}"
                        src="{{ __('pages/products/meeting/home.content.industries.image') }}"
                    />
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gray-100">
        <div
            class="container grid grid-cols-1 items-center gap-5 px-3 py-3 lg:grid-cols-2 lg:gap-12 lg:px-10"
        >
            <div>
                <img
                    loading="lazy"
                    data-aos="fade-up"
                    class="mx-auto w-full max-w-md"
                    src="{{ __('pages/products/meeting/home.content.hiw.image') }}"
                    title="{{ __('pages/products/meeting/home.content.hiw.title') }}"
                    alt="{{ __('pages/products/meeting/home.content.hiw.image_alt') }}"
                />
            </div>
            <div
                id="hiw"
                class="order-first max-w-7xl px-4 py-10 sm:px-6 sm:py-16 lg:order-last lg:px-8 lg:py-24"
            >
                <div class="mx-auto max-w-2xl text-center">
                    <h2
                        class="text-2xl font-bold leading-tight text-black sm:text-3xl"
                    >
                        {{ __('pages/products/meeting/home.content.hiw.title') }}
                    </h2>
                </div>

                <ul class="mx-auto mt-16 max-w-md space-y-12">
                    @php($delay = 200)
                    @foreach (__('pages/products/meeting/home.content.hiw.items') as $item)
                        @php($delay += 200)
                        <li class="relative flex items-start">
                            @unless ($loop->last)
                                <div
                                    class="absolute left-8 top-14 -ml-0.5 mt-0.5 h-full w-px border-l-4 border-dotted border-gray-300"
                                    aria-hidden="true"
                                ></div>
                            @endunless

                            <div
                                data-aos="flip-left"
                                data-aos-anchor="#hiw"
                                data-aos-easing="ease-out-cubic"
                                data-aos-delay="{{ $delay }}"
                                class="relative flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-white shadow"
                            >
                                <x-filament::icon
                                    :icon="$item['icon']"
                                    class="h-10 w-10 text-primary-600"
                                />
                            </div>
                            <div
                                data-aos="fade-right"
                                data-aos-anchor="#hiw"
                                data-aos-easing="ease-out-cubic"
                                data-aos-delay="{{ $delay }}"
                                class="ml-6"
                            >
                                <h3 class="text-lg font-semibold text-black">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="mt-4 text-base text-gray-600">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="overflow-hidden bg-gray-50 pt-10 sm:pt-16 lg:pt-24">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-xl text-center">
                <h2
                    class="text-3xl font-bold leading-tight text-black sm:text-4xl lg:text-5xl"
                >
                    {{ __('pages/products/meeting/home.content.cta_section.title') }}
                </h2>
                <p class="mt-4 text-base leading-relaxed text-gray-600">
                    {{ __('pages/products/meeting/home.content.cta_section.description') }}
                </p>
            </div>

            <div
                class="mx-auto flex max-w-7xl items-center justify-center py-3"
            >
                <x-filament::button
                    size="xl"
                    color="primary"
                    tag="a"
                    :href="__('pages/products/meeting/home.content.cta_section.cta.url')"
                >
                    {{ __('pages/products/meeting/home.content.cta_section.cta.label') }}
                </x-filament::button>
            </div>

            <div
                data-aos="fade-up"
                data-aos-easing="ease-out-cubic"
                class="mt-10 sm:mt-16"
            >
                <img
                    loading="lazy"
                    class="mx-auto -mb-16 w-full max-w-3xl rounded-lg shadow-xl"
                    src="{{ __('pages/products/meeting/home.content.cta_section.image') }}"
                    title="{{ __('pages/products/meeting/home.content.cta_section.title') }}"
                    alt="{{ __('pages/products/meeting/home.content.cta_section.image_alt') }}"
                />
            </div>
        </div>
    </section>
</div>
