<x-filament-panels::page>
    @if ($this->table->getColumns())
        <div class="flex flex-col gap-y-6">
            <x-filament-panels::resources.tabs />

            {{ $this->table }}
        </div>
    @endif
</x-filament-panels::page>
