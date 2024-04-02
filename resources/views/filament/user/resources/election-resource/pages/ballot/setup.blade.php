<x-filament-panels::page>
    @if(filled($pendingStep = $this->getPendingStep()))
        <x-filament.election.setup-steps
            :pending-step="$pendingStep"
            :current-step="$this->getCurrentStep()"
        />
    @endif

    {{ $this->infolist }}
</x-filament-panels::page>
