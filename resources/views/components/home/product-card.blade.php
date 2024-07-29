@php
    use App\Data\Home\ProductCardData;

    /** @var ProductCardData $data */
@endphp

@props([
    'data',
])

<div
    class="transform rounded-2xl border border-gray-200 bg-[#FAF9F6] shadow hover:-translate-y-1 hover:shadow-lg"
>
    <div class="flex h-full flex-col justify-between p-4 md:space-y-6 md:p-8">
        <h5
            class="mb-2 text-center text-2xl font-bold tracking-tight"
            style="color: {{ $data->title_color }}"
        >
            {{ $data->title }}
        </h5>
        <p
            class="mb-3 text-center font-normal text-gray-700 dark:text-gray-400"
        >
            {{ $data->description }}
        </p>
        <x-filament::button
            size="xl"
            :outlined="true"
            color="primary"
            class="w-full !rounded-full"
            href="{{ $data->cta_url }}"
        >
            {{ $data->cta_label }}
        </x-filament::button>
    </div>
</div>
