<x-filament-panels::page>
    @if(filled($pendingStep = $this->getPendingStep()))
        <x-filament.election.setup-steps
            :pending-step="$pendingStep"
            :current-step="$this->getCurrentStep()"
        />
    @endif

    <x-filament-panels::form
        wire:submit="save"
    >
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
