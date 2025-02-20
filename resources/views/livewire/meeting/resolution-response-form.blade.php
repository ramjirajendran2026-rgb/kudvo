<div class="space-y-6">
    <header autofocus class="space-y-2">
        @if ($this->isPreview)
            <div class="flex items-center justify-center gap-2">
                <x-filament::badge color="warning">Preview</x-filament::badge>
            </div>
        @endif

        <h1
            class="text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl"
        >
            {{ $this->getHeading() }}
        </h1>

        <div
            class="mt-2 w-full text-center text-lg text-gray-600 dark:text-gray-400"
        >
            {!! $this->getSubheading() !!}
        </div>
    </header>

    @if ($this->isSubmitted)
        <x-filament::section compact>
            <x-filament.state
                :heading="$this->isPreview ? 'Preview completed' : 'Submitted successfully'"
                description="Thank you for submitting your votes."
                icon="heroicon-o-document-check"
            />
        </x-filament::section>
    @else
        @php($notice = $this->getNotice())

        @if (filled($notice))
            <x-filament::section>
                <div class="prose max-w-none">
                    {!! $notice !!}
                </div>
            </x-filament::section>
        @endif

        <form wire:submit="proceed">
            {{ $this->form }}
        </form>
    @endif

    <x-filament-actions::modals />
</div>
