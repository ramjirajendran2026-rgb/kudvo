@php use App\Enums\ElectionSetupStep; @endphp
<x-filament-panels::page
    class="space-y-6"
>
    @if(filled($pendingStep = $this->getPendingStep()))
        <x-filament.election.setup-steps
            :pending-step="$pendingStep"
            :current-step="$this->getCurrentStep()"
        />
    @endif

    @if($state = $this->getStateHeading())
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
