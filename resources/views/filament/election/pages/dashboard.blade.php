<x-filament-panels::page :full-height="true">
    @if($state = $this->getStateHeading())
        <x-filament::section class="my-auto">
            <x-filament.state
                :actions="$this->getCachedStateActions()"
                :description="$this->getStateDescription()"
                :heading="$state"
                :icon="$this->getStateIcon()"
            />
        </x-filament::section>
    @endif
</x-filament-panels::page>
