<x-filament-panels::page>
    <x-filament::tabs label="View mode">
        <x-filament::tabs.item
            icon="heroicon-m-list-bullet"
            :active="! $chartView"
            wire:click="$set('chartView', false)"
        >
            List
        </x-filament::tabs.item>

        <x-filament::tabs.item
            icon="heroicon-m-chart-pie"
            :active="$chartView"
            wire:click="$set('chartView', true)"
        >
            Chart
        </x-filament::tabs.item>
    </x-filament::tabs>

    @if (! $chartView)
        {{ $this->infolist }}
    @endif
</x-filament-panels::page>
