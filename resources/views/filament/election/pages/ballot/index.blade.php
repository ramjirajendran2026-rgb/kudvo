<x-filament-panels::page
    :full-height="true"
    x-data="{}"
    x-on:flash-session-timeout="
        setTimeout(
            () => $dispatch('session-expired'),
            10000,
        );
    "
    x-on:scroll-to-top.window="
        setTimeout(
            () =>
                document.querySelector('main').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                    inline: 'start',
                }),
            200,
        )
    "
>
    <form wire:submit="submit" class="my-auto">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
