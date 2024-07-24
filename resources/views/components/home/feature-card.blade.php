@php
    use App\Data\Home\FeatureCardData;

    /** @var FeatureCardData $data */
@endphp

@props([
    'data',
])

<div class="space-y-2 rounded-xl bg-white p-2 shadow-sm dark:bg-gray-900">
    <img
        src="{{ $data->image }}"
        alt="{{ $data->title }}"
        title="{{ $data->title }}"
        class="aspect-square w-full rounded-xl"
    />
    <div class="space-y-2 p-2 md:p-4">
        <h5 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ $data->title }}
        </h5>
        <ul class="space-y-2 text-base text-gray-600 dark:text-white">
            @foreach ($data->points as $point)
                <li class="flex items-start gap-2">
                    <x-filament::icon
                        icon="heroicon-s-check"
                        class="h-6 w-6 shrink-0 text-primary-500"
                    />
                    <span>{{ $point }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
