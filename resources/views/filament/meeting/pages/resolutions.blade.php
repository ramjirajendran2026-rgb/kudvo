<x-filament-panels::page :full-height="true">
    @if ($this->canSubmit())
        @php($notice = $this->getNotice())

        @if (filled($notice))
            <x-filament::section>
                <div class="prose max-w-none">
                    {!! $notice !!}
                </div>
            </x-filament::section>
        @endif

        <form wire:submit="submit">
            {{ $this->form }}
        </form>
    @elseif ($state = $this->getStateHeading())
        <x-filament::section class="my-auto print:hidden">
            <x-filament.state
                :actions="$this->getCachedStateActions()"
                :description="$this->getStateDescription()"
                :heading="$state"
                :icon="$this->getStateIcon()"
            />
        </x-filament::section>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>
