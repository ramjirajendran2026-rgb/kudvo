<x-filament-panels::page>
    @if(filled($pendingStep = $this->getPendingStep()))
        <x-filament.election.setup-steps
                :pending-step="$pendingStep"
                :current-step="$this->getCurrentStep()"
        />
    @endif

    <div class="flex flex-col gap-y-6">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
