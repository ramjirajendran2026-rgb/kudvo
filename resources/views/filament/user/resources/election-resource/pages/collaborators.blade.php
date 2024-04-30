<x-filament-panels::page>
    @if($this->hasReadAccess())
        <div class="flex flex-col gap-y-6">
            {{ $this->table }}
        </div>
    @else
        <x-filament::section>
            <x-filament.state
                heading="Access Denied"
                description="You do not have permission to access this page."
                icon="heroicon-o-no-symbol"
            />
        </x-filament::section>
    @endif
</x-filament-panels::page>
