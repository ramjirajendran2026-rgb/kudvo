<x-filament-panels::page>
    <x-filament-panels::form
        :wire:key="$this->getId() . '.form'"
        class="w-full items-center max-w-lg mx-auto"
        onsubmit="return false"
    >
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
