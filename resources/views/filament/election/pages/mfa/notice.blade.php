<x-filament-panels::page :full-height="true">
    <x-filament-panels::form
        :wire:key="$this->getId() . '.form'"
        wire:submit="submit"
        class="w-full items-center max-w-lg mx-auto my-auto"
    >
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
