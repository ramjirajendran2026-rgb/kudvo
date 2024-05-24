@php
    use App\Data\Products\Election\BenefitData;
    use App\Data\Products\Election\HowItWorksData;
    use App\Models\ElectionPlan;

    $benefits = collect(__('pages/products/election/home.content.benefits.items'))->map(fn (array $item) => BenefitData::from($item));
    $howItWorks = collect(__('pages/products/election/home.content.how_it_works.items'))->map(fn (array $item) => HowItWorksData::from($item));
    $additionalSections = __('pages/products/election/home.content.additional_sections');
@endphp

<main class="pg-el pg-home h-full w-full">
    <section id="hero" class="py-16">
        <div class="mx-auto flex max-w-screen-lg flex-col items-center justify-center gap-6 lg:flex-row">
            <div data-aos="fade-right" class="flex-1 space-y-6 text-center lg:space-y-8 lg:text-start">
                <h2 class="font-sans text-3xl font-bold sm:text-4xl">
                    {{ __('pages/products/election/home.content.hero.title') }}
                </h2>
                <p class="text-base text-gray-700 md:text-lg">
                    {{ __('pages/products/election/home.content.hero.description') }}
                </p>
                <x-filament::button
                    size="xl"
                    color="primary"
                    tag="a"
                    :href="__('pages/products/election/home.content.hero.cta.url')"
                >
                    {{ __('pages/products/election/home.content.hero.cta.label') }}
                </x-filament::button>
            </div>
            <div data-aos="fade-left" class="flex items-center justify-center">
                <img
                    class="h-80 object-contain"
                    src="{{ asset('img/products/election/online-voting-system.webp') }}"
                    alt="Secure Online Voting with Kudvo"
                />
            </div>
        </div>
    </section>

    <section id="benefits" class="bg-white py-16">
        <div class="container grid gap-6 md:grid-cols-2 md:gap-6 lg:grid-cols-3 lg:gap-10">
            <div class="col-span-full animate-zoomIn space-y-4">
                <h3 class="text-center text-2xl font-semibold sm:text-3xl">
                    {{ __('pages/products/election/home.content.benefits.title') }}
                </h3>
                <p class="text-center text-gray-600">
                    {{ __('pages/products/election/home.content.benefits.description') }}
                </p>
            </div>

            @foreach ($benefits as $item)
                <div class="space-y-4 text-center">
                    {!! $item->icon !!}
                    <h5 class="text-lf font-semibold sm:text-xl">{{ $item->title }}</h5>
                    <p class="text-gray-600">
                        {{ $item->description }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="how-it-works" class="py-16">
        <div class="container grid gap-6 md:grid-cols-2 md:gap-6 lg:grid-cols-3 lg:gap-10">
            <div class="col-span-full space-y-4">
                <h3 class="text-center text-2xl font-semibold sm:text-3xl">
                    {{ __('pages/products/election/home.content.how_it_works.title') }}
                </h3>
                <p class="text-center text-gray-600">
                    {{ __('pages/products/election/home.content.how_it_works.description') }}
                </p>
            </div>

            @foreach ($howItWorks as $item)
                <div class="space-y-4 text-center">
                    {!! $item->icon !!}
                    <h5 class="text-lf font-semibold sm:text-xl">{{ $item->title }}</h5>
                    <p class="text-gray-600">
                        {{ $item->description }}
                    </p>
                </div>
            @endforeach

            <div class="col-span-full text-center">
                <x-filament::button
                    :href="__('pages/products/election/home.content.how_it_works.cta.url')"
                    :outlined="true"
                    tag="a"
                    size="xl"
                >
                    {{ __('pages/products/election/home.content.how_it_works.cta.label') }}
                </x-filament::button>
            </div>
        </div>
    </section>

    @foreach ($additionalSections as $section)
        <section class="{{ $loop->odd ? 'bg-white' : '' }} py-16">
            <div
                class="{{ $loop->even ? 'md:flex-row-reverse' : 'md:flex-row' }} container flex flex-col items-center justify-center gap-6"
            >
                <div class="flex flex-1 items-center justify-center" data-aos="flip-left">
                    <img src="{{ $section['image'] }}" alt="{{ $section['title'] }}" class="h-80 object-contain" />
                </div>
                <div class="flex-1 space-y-6 lg:space-y-8">
                    <h3 class="text-2xl font-semibold sm:text-3xl">
                        {{ $section['title'] }}
                    </h3>
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
            <h3 class="text-2xl font-semibold sm:text-3xl">
                {{ __('pages/products/election/home.content.cta_section.title') }}
            </h3>
            <p class="text-gray-600">
                {{ __('pages/products/election/home.content.cta_section.description') }}
            </p>
            <x-filament::button
                tag="a"
                :href="__('pages/products/election/home.content.cta_section.cta_url')"
                size="xl"
            >
                {{ __('pages/products/election/home.content.cta_section.cta_label') }}
            </x-filament::button>
        </div>
    </section>
</main>
