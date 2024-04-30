<x-filament-panels::page>
    @if(filled($pendingStep = $this->getPendingStep()))
        <x-filament.election.setup-steps
            :pending-step="$pendingStep"
            :current-step="$this->getCurrentStep()"
        />
    @endif

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
