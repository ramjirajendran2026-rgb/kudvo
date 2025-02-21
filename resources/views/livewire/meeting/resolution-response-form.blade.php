<div class="space-y-6">
    <header autofocus class="space-y-2">
        <div class="flex items-center justify-center gap-x-2">
            <x-filament::badge
                color="primary"
                icon="heroicon-m-clipboard-document"
                icon-position="after"
                class="cursor-pointer font-mono font-semibold"
                x-on:click="
                    window.navigator.clipboard.writeText('{{ $this->getMeeting()->code }}')
                    $tooltip('Copied', {
                        theme: $store.theme,
                        timeout: 2000,
                    })
                "
            >
                {{ $this->getMeeting()->code }}
            </x-filament::badge>

            @if ($this->isPreview)
                <x-filament::badge color="warning">Preview</x-filament::badge>
            @endif
        </div>

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
