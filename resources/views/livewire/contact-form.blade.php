<div class="contact-form">
    <form wire:submit="submit">
        {{ $this->form }}

        <x-filament::button type="submit" color="primary" class="mt-6 w-full" size="xl" wire:target="submit">
            {{ __('Submit') }}
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
