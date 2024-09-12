<div class="flex min-h-screen items-center justify-center px-4 py-6">
    <form wire:submit="submit" class="w-full max-w-screen-sm">
        {{ $this->form }}
    </form>

    <x-filament-actions::modals />
</div>
