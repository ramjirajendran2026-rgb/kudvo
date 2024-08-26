<main class="container flex flex-1 items-center justify-center pb-6">
    <form wire:submit="proceed" class="w-full max-w-md">
        {{ $this->form }}
    </form>

    <x-filament-actions::modals />
</main>
