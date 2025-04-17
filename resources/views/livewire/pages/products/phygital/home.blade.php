<main class="pg-pgdl overflow-hidden">
    <!-- Animated Background Shapes -->
    <div class="container fixed inset-0 -z-10">
        <div
            class="animate-float absolute left-[-35%] top-[25%] h-64 w-64 rounded-full bg-amber-300 opacity-30 mix-blend-multiply blur-xl filter md:left-[-15%] md:top-[-5%]"
        ></div>
        <div
            class="animate-float absolute right-[-25%] top-[15%] h-72 w-72 rounded-full bg-emerald-300 opacity-30 mix-blend-multiply blur-xl filter md:right-[-5%] md:top-[-15%]"
            style="animation-delay: -2s"
        ></div>
        <div
            class="animate-float absolute bottom-[5%] right-[-15%] h-80 w-80 rounded-full bg-yellow-300 opacity-30 mix-blend-multiply blur-xl filter"
            style="animation-delay: -4s"
        ></div>
    </div>

    <section id="hero" class="container mx-auto">
        <div
            class="mx-auto flex max-w-screen-lg flex-col items-center justify-center gap-6 lg:flex-row"
        >
            <div
                data-aos="fade-right"
                class="flex-1 space-y-6 text-center lg:space-y-8 lg:text-start"
            >
                <h1 class="text-3xl font-bold sm:text-4xl">
                    @lang('pages/products/phygital/home.content.hero.title')
                </h1>
                <p class="text-base text-gray-700 md:text-lg">
                    @lang('pages/products/phygital/home.content.hero.description')
                </p>
                <div
                    class="flex w-full flex-col justify-center gap-5 sm:flex-row lg:justify-start"
                >
                    <x-filament::button
                        size="xl"
                        color="primary"
                        tag="a"
                        :href="__('pages/products/election/home.content.hero.cta.url')"
                    >
                        {{ __('pages/products/election/home.content.hero.cta.label') }}
                    </x-filament::button>
                    <x-filament::button
                        size="xl"
                        color="gray"
                        tag="a"
                        :href="__('pages/products/phygital/home.content.hero.cta.url')"
                    >
                        @lang('pages/products/phygital/home.content.hero.cta.label')
                    </x-filament::button>
                </div>
            </div>
            <div
                data-aos="fade-left"
                class="flex w-full max-w-sm items-center justify-center"
            >
                <img
                    class="w-full object-contain"
                    src="@lang('pages/products/phygital/home.content.hero.image')"
                    alt="@lang('pages/products/phygital/home.content.hero.image_alt')"
                    title="@lang('pages/products/phygital/home.content.hero.title')"
                />
            </div>
        </div>
    </section>
    <section id="key-features" class="w-full bg-slate-50 py-16">
        <div class="container">
            <div
                data-aos="zoom-out-up"
                class="mx-auto flex flex-col items-center justify-center gap-4"
            >
                <div
                    class="rounded-lg border-[1px] border-primary-400 bg-primary-100 px-3 py-1 text-sm text-primary-600"
                >
                    @lang('pages/products/phygital/home.content.key_features.label')
                </div>
                <h2
                    class="text-center text-3xl font-bold tracking-tighter sm:text-4xl"
                >
                    @lang('pages/products/phygital/home.content.key_features.title')
                </h2>
                <p
                    class="max-w-screen-lg text-center md:text-lg/relaxed lg:text-lg/relaxed"
                >
                    @lang('pages/products/phygital/home.content.key_features.description')
                </p>
            </div>
            <div
                class="mt-8 flex w-full flex-col items-center justify-center md:flex-row"
            >
                <div data-aos="fade-left" class="w-full md:w-1/2">
                    <img
                        class="mx-auto size-80 object-contain"
                        src="@lang('pages/products/phygital/home.content.key_features.image')"
                        alt="@lang('pages/products/phygital/home.content.key_features.image_alt')"
                        title="@lang('pages/products/phygital/home.content.key_features.title')"
                    />
                </div>
                <div data-aos="fade-right" class="w-full md:w-1/2">
                    <ul class="grid gap-6">
                        @foreach (__('pages/products/phygital/home.content.key_features.items') as $item)
                            <li>
                                <div class="grid gap-1">
                                    <h3 class="text-xl font-bold">
                                        {{ $item['title'] }}
                                    </h3>
                                    <p>
                                        {{ $item['description'] }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="vvpat-printing" class="w-full bg-slate-50 py-16">
        <div class="container">
            <div
                data-aos="zoom-out-up"
                class="mx-auto flex flex-col items-center justify-center gap-4"
            >
                <div
                    class="rounded-lg border-[1px] border-primary-400 bg-primary-100 px-3 py-1 text-sm text-primary-600"
                >
                    @lang('pages/products/phygital/home.content.vvpat_printing.label')
                </div>
                <h2
                    class="text-center text-3xl font-bold tracking-tighter sm:text-4xl"
                >
                    @lang('pages/products/phygital/home.content.vvpat_printing.title')
                </h2>
                <p
                    class="max-w-screen-lg text-center md:text-lg/relaxed lg:text-lg/relaxed"
                >
                    @lang('pages/products/phygital/home.content.vvpat_printing.description')
                </p>
            </div>
            <div
                class="mt-8 flex w-full flex-col items-center justify-center md:flex-row"
            >
                <div data-aos="fade-left" class="w-full md:w-1/2">
                    <img
                        class="mx-auto size-80 object-contain"
                        src="@lang('pages/products/phygital/home.content.vvpat_printing.image')"
                        alt="@lang('pages/products/phygital/home.content.vvpat_printing.image_alt')"
                        title="@lang('pages/products/phygital/home.content.vvpat_printing.title')"
                    />
                </div>
                <div data-aos="fade-right" class="w-full md:w-1/2">
                    <ul class="grid gap-6">
                        @foreach (__('pages/products/phygital/home.content.vvpat_printing.items') as $item)
                            <li>
                                <div class="grid gap-1">
                                    <h3 class="text-xl font-bold">
                                        {{ $item['title'] }}
                                    </h3>
                                    <p>
                                        {{ $item['description'] }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="how-we-do" class="container mx-auto w-full">
        <div class="relative">
            <main class="relative mx-auto max-w-screen-md p-5 md:p-12 mt-6 flex flex-col justify-center overflow-hidden">
                @foreach (__('pages/products/phygital/home.content.how_we_do.videos') as $item)
                    <x-youtube-video-player video-id="{{$item['yt-video-id']}}" title="{{$item['title']}}"/>
                @endforeach
            </main>
        </div>
    </section>
    <section class="container">
        <p
            class="mx-auto w-full max-w-screen-md py-5 text-center md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed"
        >
            @lang('pages/products/phygital/home.content.footer.description')
        </p>
    </section>
</main>
