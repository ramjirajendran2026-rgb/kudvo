<div class="mx-auto w-full max-w-screen-md space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-filament::section compact>
        <h1
            class="text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl"
        >
            {{ $survey->title }}
        </h1>
    </x-filament::section>

    @if ($this->isSubmitted)
        <x-filament::section compact>
            <x-filament.state
                heading="Submitted successfully"
                description="Thank you for submitting your answers"
                icon="heroicon-o-document-check"
            />
        </x-filament::section>
    @else
        @if ($survey->description)
            <x-filament::section
                class="prose max-w-none [&_.fi-section-content>:first-child]:mt-0 [&_.fi-section-content>:last-child]:mb-0"
                compact
            >
                {!! tiptap_converter()->asHTML($survey->description) !!}
            </x-filament::section>
        @endif

        <form wire:submit="submit">
            {{ $this->form }}
        </form>
    @endif

    <x-filament.nomination.footer class="text-center" />

    <x-filament-actions::modals />
</div>
