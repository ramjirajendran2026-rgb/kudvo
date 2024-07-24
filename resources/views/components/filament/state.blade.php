@php
    use Filament\Support\Enums\Alignment;
@endphp

@props([
    'actions' => [],
    'description' => null,
    'heading',
    'icon' => null,
])

<div {{ $attributes->class(['px-6 py-12']) }}>
    <div class="mx-auto grid max-w-lg justify-items-center text-center">
        @if ($icon)
            <div class="mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                <x-filament::icon
                    :icon="$icon"
                    class="h-6 w-6 text-gray-500 dark:text-gray-400"
                />
            </div>
        @endif

        <h4
            class="text-base font-semibold leading-6 text-gray-950 dark:text-white"
        >
            {{ $heading }}
        </h4>

        @if ($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $description }}
            </p>
        @endif

        @if ($actions)
            <x-filament::actions
                :actions="$actions"
                :alignment="Alignment::Center"
                :wrap="true"
                class="mt-6"
            />
        @endif
    </div>
</div>
