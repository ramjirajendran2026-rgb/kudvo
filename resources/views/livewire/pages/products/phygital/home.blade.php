<main class="pg-pgdl h-full w-full px-4">
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
                <x-filament::button
                    size="xl"
                    color="primary"
                    tag="a"
                    :href="__('pages/products/phygital/home.content.hero.cta.url')"
                >
                    @lang('pages/products/phygital/home.content.hero.cta.label')
                </x-filament::button>
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
    <section id="key-features" class="container w-full py-16">
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
    </section>
    <section id="vvpat-printing" class="container w-full py-16">
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
    </section>
    <section id="how-we-do" class="container mx-auto w-full">
        <div class="relative">
            <main
                class="relative flex flex-col justify-center overflow-hidden bg-slate-50"
            >
                <div class="mx-auto w-full max-w-6xl px-4 py-12 md:px-6">
                    <div class="flex justify-center">
                        <!-- Modal video component -->
                        <div
                            class="[&_[x-cloak]]:hidden"
                            x-data="{ modalOpen: false }"
                        >
                            <!-- Video thumbnail -->
                            <button
                                data-aos="zoom-out-up"
                                class="group relative flex items-center justify-center rounded-3xl focus:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                @click="modalOpen = true"
                                aria-controls="modal"
                                aria-label="Watch the video"
                            >
                                <img
                                    class="rounded-3xl shadow-2xl transition-shadow duration-300 ease-in-out"
                                    src="@lang('pages/products/phygital/home.content.how_we_do.video.thumbnail.src')"
                                    width="768"
                                    height="432"
                                    alt="@lang('pages/products/phygital/home.content.how_we_do.video.thumbnail.alt')"
                                />
                                <!-- Play icon -->
                                <svg
                                    class="pointer-events-none absolute transition-transform duration-300 ease-in-out group-hover:scale-110"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="72"
                                    height="72"
                                >
                                    <circle
                                        class="fill-white"
                                        cx="36"
                                        cy="36"
                                        r="36"
                                        fill-opacity=".8"
                                    />
                                    <path
                                        class="fill-indigo-500 drop-shadow-2xl"
                                        d="M44 36a.999.999 0 0 0-.427-.82l-10-7A1 1 0 0 0 32 29V43a.999.999 0 0 0 1.573.82l10-7A.995.995 0 0 0 44 36V36c0 .001 0 .001 0 0Z"
                                    />
                                </svg>
                            </button>
                            <!-- End: Video thumbnail -->

                            <!-- Modal backdrop -->
                            <div
                                class="fixed inset-0 z-[99999] bg-black bg-opacity-50 transition-opacity"
                                x-show="modalOpen"
                                x-transition:enter="transition duration-200 ease-out"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition duration-100 ease-out"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                aria-hidden="true"
                                x-cloak
                            ></div>
                            <!-- End: Modal backdrop -->

                            <!-- Modal dialog -->
                            <div
                                id="modal"
                                class="fixed inset-0 z-[99999] flex px-4 py-6 md:px-6"
                                role="dialog"
                                aria-modal="true"
                                x-show="modalOpen"
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="scale-75 opacity-0"
                                x-transition:enter-end="scale-100 opacity-100"
                                x-transition:leave="transition duration-200 ease-out"
                                x-transition:leave-start="scale-100 opacity-100"
                                x-transition:leave-end="scale-75 opacity-0"
                                x-cloak
                            >
                                <div
                                    class="mx-auto flex h-full max-w-5xl items-center"
                                >
                                    <div
                                        class="aspect-video max-h-full w-full overflow-hidden rounded-3xl bg-black shadow-2xl"
                                        @click.outside="modalOpen = false"
                                        @keydown.escape.window="modalOpen = false"
                                    >
                                        <video
                                            x-init="$watch('modalOpen', (value) => (value ? $el.play() : $el.pause()))"
                                            width="1920"
                                            height="1080"
                                            loop
                                            controls
                                        >
                                            <source
                                                loading="lazy"
                                                src="@lang('pages/products/phygital/home.content.how_we_do.video.src')"
                                                type="video/mp4"
                                            />
                                            Your browser does not support the
                                            video tag.
                                        </video>
                                    </div>
                                </div>
                            </div>
                            <!-- End: Modal dialog -->
                        </div>
                        <!-- End: Modal video component -->
                    </div>
                </div>
            </main>
        </div>
    </section>
    <section class="container">
        <p
            class="mx-auto w-full max-w-screen-md py-16 text-center md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed"
        >
            @lang('pages/products/phygital/home.content.footer.description')
        </p>
    </section>
</main>
