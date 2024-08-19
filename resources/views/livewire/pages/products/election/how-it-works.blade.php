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

    <section id="video" class="py-16">
        <div class="container">
            <video
                class="mx-auto w-full max-w-screen-lg rounded-2xl"
                autoplay
                muted
                loop
            >
                <source
                    src="{{ __('pages/products/election/how-it-works.content.video.url') }}"
                    type="video/mp4"
                />

                {{ __('pages/products/election/how-it-works.content.video.unsupported_label') }}
            </video>
        </div>
    </section>
</main>
