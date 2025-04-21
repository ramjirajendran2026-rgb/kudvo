@php
    use App\Data\Home\FeatureCardData;

    /** @var FeatureCardData $data */
@endphp

@props(['data'])

<div
    data-aos="fade-up"
    class="rounded-2xl overflow-hidden border border-primary-100 glass neumorph transition-all shadow-md hover:shadow-lg bg-white/80 backdrop-blur-md font-sans"
>
    {{-- Header Section with Fixed Height --}}
    <div class="flex items-center gap-4 bg-primary-100 p-4 sm:p-6 min-h-[96px]">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img
                loading="lazy"
                src="{{ $data->image }}"
                alt="{{ $data->image_alt }}"
                title="{{ $data->title }}"
                class="h-12 w-12 object-contain"
            />
        </div>
        <h3 class="text-lg sm:text-xl font-extrabold text-gray-800 leading-snug">
            {{ $data->title }}
        </h3>
    </div>

    {{-- Points List --}}
    <ul class="p-5 sm:p-6 space-y-3 text-sm sm:text-base text-gray-700">
        @foreach ($data->points as $point)
            <li class="flex items-start gap-2">
                <x-heroicon-s-check class="h-5 w-5 text-primary-500" />
                <span class="leading-snug">{{ $point }}</span>
            </li>
        @endforeach
    </ul>
</div>
