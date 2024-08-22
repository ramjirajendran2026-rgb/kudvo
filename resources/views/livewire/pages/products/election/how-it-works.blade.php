<main class="pg-el pg-hiw h-full w-full">
    <section
        class="relative h-[calc(100vh-5rem)] w-full bg-cover bg-no-repeat"
        style="
            background-image: url('{{ __('pages/products/election/how-it-works.content.hero.bg_image') }}');
        "
    >
        <div
            class="absolute inset-0 flex flex-col items-center justify-center gap-6 bg-black bg-opacity-50 p-4 text-center text-white sm:p-6"
        >
            <h1 class="font-sans text-4xl font-bold md:text-6xl">
                {{ __('pages/products/election/how-it-works.content.hero.title') }}
            </h1>
            <p class="text-base md:text-lg">
                {!! __('pages/products/election/how-it-works.content.hero.description') !!}
            </p>
            <div class="flex items-center justify-center gap-6">
                <x-filament::button
                    size="xl"
                    color="primary"
                    tag="a"
                    :href="__('pages/products/election/how-it-works.content.hero.cta.url')"
                >
                    {{ __('pages/products/election/how-it-works.content.hero.cta.label') }}
                </x-filament::button>
            </div>
        </div>
    </section>

    <section id="steps" class="bg-white py-16">
        <div
            class="container grid gap-6 md:grid-cols-2 md:gap-6 lg:grid-cols-3 lg:gap-10"
        >
            @foreach (__('pages/products/election/how-it-works.content.steps') as $item)
                <div class="space-y-4 text-center">
                    {!! $item['icon'] !!}
                    <h2 class="text-lf font-semibold sm:text-xl">
                        {{ $item['title'] }}
                    </h2>
                    <p class="text-gray-600">
                        {{ $item['description'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container mx-auto w-full" id="online-voting-video">
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
                                    src="{{ asset('img/products/election/how-to-setup-online-election.webp') }}"
                                    width="768"
                                    height="432"
                                    alt="online voting video thumbnail"
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
                                                src="{{ __('pages/products/election/how-it-works.content.video.url') }}"
                                                type="video/mp4"
                                            />
                                            {{ __('pages/products/election/how-it-works.content.video.unsupported_label') }}
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
</main>
