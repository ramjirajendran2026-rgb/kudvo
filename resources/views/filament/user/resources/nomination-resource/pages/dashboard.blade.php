<x-filament-panels::page class="space-y-6">
    @if ($state = $this->getStateHeading())
        <x-filament::section>
            <x-filament.state
                :actions="$this->getCachedStateActions()"
                :description="$this->getStateDescription()"
                :heading="$state"
                :icon="$this->getStateIcon()"
            />
        </x-filament::section>
    @endif
</x-filament-panels::page>
