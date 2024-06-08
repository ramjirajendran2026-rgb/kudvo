<x-filament-panels::page
    :full-height="true"
    x-data="{
        playBeep() {
            const audio = this.$refs.audio;
            audio.play();
        }
    }"
    x-on:flash-session-timeout="
        setTimeout(
            () => $dispatch('session-expired'),
            $event.detail.interval,
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
    x-on:play-beep="playBeep()"
    x-on:do-logout.window="$dispatch('session-expired')"
>
    <form wire:submit="submit" class="my-auto">
        {{ $this->form }}
    </form>

    <audio x-ref="audio" src="{{ asset('assets/long-beep.mp3') }}"></audio>
</x-filament-panels::page>
