@php
    use App\Data\Home\ProductCardData;

    /** @var ProductCardData $data */
@endphp

@props([
    'data',
])

<div
    data-aos="flip-right"
    class="transform rounded-2xl border border-gray-200 glass shadow-md hover:-translate-y-1 hover:shadow-lg transition-all duration-200 bg-white/80 backdrop-blur-md"
>
    <div class="flex h-full flex-col justify-between p-4 sm:p-6 md:space-y-6 md:p-8 font-sans">
        <h5
            class="mb-2 text-center text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight text-gray-900"
            style="color: {{ $data->title_color }}"
        >
            {{ $data->title }}
        </h5>
        <p
            class="mb-3 text-center text-base sm:text-lg font-normal text-gray-700"
        >
            {{ $data->description }}
        </p>
        <x-filament::button
            size="xl"
            :outlined="true"
            color="primary"
            class="w-full !rounded-full btn-primary focus-outline text-base sm:text-lg py-3 mt-2"
            style="font-family: 'Inter', system-ui, sans-serif;"
            :disabled="blank($data->cta_url)"
            :tag="blank($data->cta_url) ? 'button' : 'a'"
            href="{{ $data->cta_url }}"
        >
            {{ $data->cta_label }}
        </x-filament::button>
    </div>
</div>
