<div class="p-2 lg:p-6">
    <x-filament::section class="mx-auto max-w-4xl">
        <h1 class="px-2 py-4 text-center text-2xl font-bold">
            {{ $survey->name }}
        </h1>

        <div class="prose max-w-none">
            {!! $survey->settings['description'] ?? null !!}
        </div>

        <form wire:submit="submit">
            {{ $this->form }}
        </form>
    </x-filament::section>

    <x-filament-actions::modals />
</div>
