<div
    class="mx-auto flex w-full max-w-screen-md flex-col justify-center gap-y-6 px-4 py-6 sm:px-6 lg:px-8"
>
    <h1
        class="text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl"
    >
        {{ $survey->title }}
    </h1>

    @if ($this->isSubmitted)
        <x-filament::section compact>
            <x-filament.state
                :heading="$this->getSuccessHeading()"
                :description="$this->getSuccessDescription()"
                icon="heroicon-o-document-check"
            />
        </x-filament::section>
    @else
        @if ($this->isPreview)
            <div
                class="rounded-xl border border-warning-200 bg-warning-100 px-4 py-3 text-warning-700 shadow"
                role="alert"
            >
                This survey is in preview mode. You can continue to submit your
                answers, but they will not be saved.
            </div>
        @endif

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
