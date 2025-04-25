@php
    use App\Data\Home\ProductCardData;

    /** @var ProductCardData $data */
@endphp

@props([
    'data',
])

<div
    data-aos="flip-right"
    class="glass transform rounded-2xl border border-gray-200 bg-white/80 shadow-md backdrop-blur-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg"
>
    <div
        class="flex h-full flex-col justify-between p-4 font-sans sm:p-6 md:space-y-6 md:p-8"
    >
        <h5
            class="mb-2 text-center text-2xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-3xl"
            style="color: {{ $data->title_color }}"
        >
            {{ $data->title }}
        </h5>
        <p
            class="mb-3 text-center text-base font-normal text-gray-700 sm:text-lg"
        >
            {{ $data->description }}
        </p>
        <x-filament::button
            size="xl"
            :outlined="true"
            color="primary"
            class="btn-primary focus-outline mt-2 w-full !rounded-full py-3 text-base sm:text-lg"
            style="font-family: 'Inter', system-ui, sans-serif"
            :disabled="blank($data->cta_url)"
            :tag="blank($data->cta_url) ? 'button' : 'a'"
            href="{{ $data->cta_url }}"
        >
            {{ $data->cta_label }}
        </x-filament::button>
    </div>
</div>
