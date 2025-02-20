<x-filament-panels::page :full-height="true">
    <x-filament::section class="my-auto print:hidden">
        <x-filament.state
            :actions="$this->getCachedStateActions()"
            :description="$this->getStateDescription()"
            :heading="$this->getStateHeading()"
            :icon="$this->getStateIcon()"
        />
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
