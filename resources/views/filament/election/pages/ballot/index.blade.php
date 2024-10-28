<x-filament-panels::page
    :full-height="true"
    x-data="{
        inactivityThreshold: 5 * 60 * 1000, // 5 minutes
        inactivityTimeout: null,
        playBeep() {
            const audio = this.$refs.audio;
            audio.play();
        },
        resetInactivityTimeout() {
            clearTimeout(this.inactivityTimeout)

            this.inactivityTimeout = setTimeout(() => {
                document.querySelector('main').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                    inline: 'start',
                }),

                setTimeout(() => {
                    location.reload()
                }, 2000)
            }, this.inactivityThreshold)
        }
    }"
    x-on:mousemove.document="resetInactivityTimeout()"
    x-on:touchstart.document="resetInactivityTimeout()"
    x-on:keypress.document="resetInactivityTimeout()"
    x-on:click.document="resetInactivityTimeout()"
    x-on:scroll.document="resetInactivityTimeout()"
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
