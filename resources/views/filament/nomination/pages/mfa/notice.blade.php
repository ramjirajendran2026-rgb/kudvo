<x-filament-panels::page>
    <x-filament-panels::form
        :wire:key="$this->getId().'.form'"
        wire:submit="submit"
        class="mx-auto w-full max-w-lg items-center"
    >
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
