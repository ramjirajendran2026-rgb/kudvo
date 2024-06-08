<x-filament-panels::page
    :full-height="true"
    x-data="{
        playBeep: {{ \Illuminate\Support\Js::from(data: $this->playBeep) }},
        autoPrint: {{ \Illuminate\Support\Js::from(data: $this->autoPrint) }},
        init() {
            if (this.playBeep) {
                this.$refs.audio.play();
            }

            if (this.autoPrint) {
                setTimeout(() => {
                    window.print();
                }, 1000);
            }
        }
    }"
    class="fi-pg-home"
>
    @if ($state = $this->getStateHeading())
        <x-filament::section class="my-auto print:hidden">
            <x-filament.state
                :actions="$this->getCachedStateActions()"
                :description="$this->getStateDescription()"
                :heading="$state"
                :icon="$this->getStateIcon()"
            />
        </x-filament::section>
    @endif

    @if ($this->sessionVoteIds)
        {{ $this->form }}
    @endif

    <audio x-ref="audio" src="{{ asset('assets/long-beep.mp3') }}"></audio>
</x-filament-panels::page>
