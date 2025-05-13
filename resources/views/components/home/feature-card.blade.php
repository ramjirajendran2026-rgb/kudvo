@php
    use App\Data\Home\FeatureCardData;

    /** @var FeatureCardData $data */
@endphp

@props(['data'])

<div
    data-aos="fade-up"
    class="overflow-hidden rounded-2xl border border-primary-100 bg-white/80 font-sans shadow-md backdrop-blur-md transition-all glass neumorph hover:shadow-lg"
>
    {{-- Header Section with Fixed Height --}}
    <div class="flex min-h-[96px] items-center gap-4 bg-primary-100 p-4 sm:p-6">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img
                loading="lazy"
                src="{{ $data->image }}"
                alt="{{ $data->image_alt }}"
                title="{{ $data->title }}"
                class="h-12 w-12 object-contain"
            />
        </div>
        <h3
            class="text-lg font-extrabold leading-snug text-gray-800 sm:text-xl"
        >
            {{ $data->title }}
        </h3>
    </div>

    {{-- Points List --}}
    <ul class="space-y-3 p-5 text-sm text-gray-700 sm:p-6 sm:text-base">
        @foreach ($data->points as $point)
            <li class="flex items-start gap-2">
                <x-heroicon-s-check class="h-5 w-5 text-primary-500" />
                <span class="leading-snug">{{ $point }}</span>
            </li>
        @endforeach
    </ul>
</div>
