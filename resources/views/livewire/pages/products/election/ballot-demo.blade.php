<main class="container flex flex-1 items-center justify-center pb-6">
    @if (blank($this->getElection()))
        <x-filament.state
            icon="heroicon-o-information-circle"
            heading="No demo election available."
        />
    @else
        <form wire:submit="proceed" class="w-full max-w-md">
            {{ $this->form }}
        </form>
    @endif

    <x-filament-actions::modals />
</main>
