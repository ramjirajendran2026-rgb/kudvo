@php
    use App\Data\Products\Election\BenefitData;
    use App\Data\Products\Election\HowItWorksData;
    use App\Models\ElectionPlan;

    $benefits = collect(__('pages/products/election/home.content.benefits.items'))->map(fn (array $item) => BenefitData::from($item));
    $howItWorks = collect(__('pages/products/election/home.content.how_it_works.items'))->map(fn (array $item) => HowItWorksData::from($item));
    $additionalSections = __('pages/products/election/home.content.additional_sections');
@endphp

<main class="pg-el pg-home overflow-hidden">
    <!-- Animated Background Shapes -->
    <div class="container fixed inset-0 -z-10">
        <div
            class="animate-float absolute left-[-35%] top-[25%] h-64 w-64 rounded-full bg-blue-300 opacity-30 mix-blend-multiply blur-xl filter md:left-[-5%] md:top-[75%]"
        ></div>
        <div
            class="animate-float absolute right-[-25%] top-[15%] h-72 w-72 rounded-full bg-violet-300 opacity-30 mix-blend-multiply blur-xl filter md:right-[15%] md:top-[15%]"
            style="animation-delay: -2s"
        ></div>
        <div
            class="animate-float absolute bottom-[5%] right-[-15%] h-80 w-80 rounded-full bg-green-300 opacity-30 mix-blend-multiply blur-xl filter md:bottom-[5%] md:right-[5%]"
            style="animation-delay: -4s"
        ></div>
    </div>
    <section id="hero" class="px-2 py-16 md:px-0">
        <div
            class="mx-auto flex max-w-screen-lg flex-col items-center justify-center gap-6 lg:flex-row"
        >
            <div
                data-aos="fade-right"
                class="flex-1 space-y-6 text-center lg:space-y-8 lg:text-start"
            >
                <h1 class="font-sans text-3xl font-bold sm:text-4xl">
                    {{ __('pages/products/election/home.content.hero.title') }}
                </h1>
                <p class="text-base text-gray-700 md:text-lg">
                    {{ __('pages/products/election/home.content.hero.description') }}
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
                        tag="a"
                        color="gray"
                        :href="__('pages/products/election/home.content.cta_section.cta_url')"
                        size="xl"
                        title="Kudvo Sign up"
                    >
                        {{ __('pages/products/election/home.content.cta_section.cta_label') }}
                    </x-filament::button>
                </div>
            </div>
            <div data-aos="fade-left" class="flex items-center justify-center">
                <img
                    class="size-80 object-contain"
                    src="{{ asset('img/products/election/online-voting-system.webp') }}"
                    alt="{{ __('pages/products/election/home.content.hero.image_alt') }}"
                    title="{{ __('pages/products/election/home.content.hero.title') }}"
                />
            </div>
        </div>
    </section>

    <section id="benefits" class="bg-white py-16">
        <div class="container flex flex-wrap justify-center gap-6 lg:gap-10">
            <!-- Section Title -->
            <div class="animate-zoomIn w-full space-y-4">
                <h2 class="text-center text-2xl font-semibold sm:text-3xl">
                    {{ __('pages/products/election/home.content.benefits.title') }}
                </h2>
                <p class="text-center text-gray-600">
                    {{ __('pages/products/election/home.content.benefits.description') }}
                </p>
            </div>

            <!-- Benefits List -->
            @foreach ($benefits as $item)
                <div
                    class="flex w-full flex-col items-center space-y-4 text-center sm:w-[calc(50%-1.5rem)] lg:w-[calc(33.333%-2.5rem)]"
                >
                    <!-- Icon -->
                    {!! $item->icon !!}

                    <!-- Title -->
                    <h3 class="text-lg font-semibold sm:text-xl">
                        {{ $item->title }}
                    </h3>

                    <!-- Description -->
                    <p class="text-gray-600">
                        {{ $item->description }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="how-it-works" class="py-16">
        <div class="container flex flex-col items-center gap-6">
            <!-- Section Title -->
            <div class="w-full space-y-4">
                <h2 class="text-center text-2xl font-semibold sm:text-3xl">
                    {{ __('pages/products/election/home.content.how_it_works.title') }}
                </h2>
                <p class="text-center text-gray-600">
                    {{ __('pages/products/election/home.content.how_it_works.description') }}
                </p>
            </div>

            <!-- How It Works Items -->
            <div class="flex w-full flex-wrap justify-center gap-6 lg:gap-10">
                @foreach ($howItWorks as $item)
                    <div
                        class="flex w-full flex-col items-center space-y-4 text-center sm:w-[calc(50%-1.5rem)] lg:w-[calc(33.333%-2.5rem)]"
                    >
                        <!-- Icon -->
                        {!! $item->icon !!}

                        <!-- Title -->
                        <h3 class="text-lf font-semibold sm:text-xl">
                            {{ $item->title }}
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600">
                            {{ $item->description }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap justify-center gap-4 text-center">
                <x-filament::button
                    :href="__('pages/products/election/home.content.how_it_works.cta.url')"
                    :outlined="true"
                    tag="a"
                    size="xl"
                    title="How it's works page link"
                >
                    {{ __('pages/products/election/home.content.how_it_works.cta.label') }}
                </x-filament::button>
                <x-filament::button
                    size="xl"
                    :outlined="true"
                    tag="a"
                    :href="__('pages/products/election/how-it-works.content.hero.cta.url')"
                >
                    {{ __('pages/products/election/how-it-works.content.hero.cta.label') }}
                </x-filament::button>
            </div>
        </div>
    </section>

    @foreach ($additionalSections as $section)
        <section class="{{ $loop->odd ? 'bg-white' : '' }} py-16">
            <div
                class="{{ $loop->even ? 'md:flex-row-reverse' : 'md:flex-row' }} container flex flex-col items-center justify-center gap-6"
            >
                <div
                    class="flex flex-1 items-center justify-center"
                    data-aos="flip-left"
                >
                    <img
                        loading="lazy"
                        src="{{ $section['image'] }}"
                        alt="{{ $section['image_alt'] }}"
                        class="size-80 object-contain"
                        title="{{ $section['title'] }}"
                    />
                </div>
                <div class="flex-1 space-y-6 lg:space-y-8">
                    <h2 class="text-2xl font-semibold sm:text-3xl">
                        {{ $section['title'] }}
                    </h2>
                    <p>
                        {{ $section['description'] }}
                    </p>
                    <div class="space-y-4">
                        @foreach ($section['items'] as $item)
                            <div class="flex gap-2">
                                {!! $item['icon'] !!}
                                <span class="text-wrap text-gray-600">
                                    {{ $item['value'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endforeach

    <section id="pricing" class="py-16">
        <div class="container space-y-4 text-center">
            <h3 class="text-2xl font-semibold sm:text-3xl">
                {{ __('pages/products/election/home.content.pricing.title') }}
            </h3>
            <p class="text-gray-600">
                {{ __('pages/products/election/home.content.pricing.description') }}
            </p>

            @livewire('election.pricing-table')
        </div>
    </section>

    <section id="cta-section" class="bg-white py-16">
        <div class="container space-y-4 text-center">
            <h2 class="text-2xl font-semibold sm:text-3xl">
                {{ __('pages/products/election/home.content.cta_section.title') }}
            </h2>
            <p class="text-gray-600">
                {{ __('pages/products/election/home.content.cta_section.description') }}
            </p>
            <x-filament::button
                tag="a"
                :href="__('pages/products/election/home.content.cta_section.cta_url')"
                size="xl"
                title="Kudvo Sign up"
            >
                {{ __('pages/products/election/home.content.cta_section.cta_label') }}
            </x-filament::button>
        </div>
    </section>
</main>
