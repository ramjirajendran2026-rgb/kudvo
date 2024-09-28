<div class="flex min-h-screen items-center justify-center px-4 py-6">
    @if($isClosed)
        <x-filament::section
            heading="Meeting Registration 29 Sep, 2024"
        >
            <div class="text-lg text-gray-600 dark:text-gray-400">
                Sorry, registration for this meeting is closed.
            </div>
        </x-filament::section>
    @else
        <form wire:submit="submit" class="w-full max-w-screen-sm">
            {{ $this->form }}
        </form>
    @endif

    <x-filament-actions::modals />
</div>
