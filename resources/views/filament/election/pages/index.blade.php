<x-filament-panels::page
    :full-height="true"
    x-data="{
        autoPrint: {{ \Illuminate\Support\Js::from(data: $this->autoPrint) }},
        voteCastedMessage: 'Thank you. Your vote has been submitted successfully',
        playVoteCastedMessage: {{ \Illuminate\Support\Js::from(data: $this->playVoteCastedMessage) }},
        printBallot() {
            setTimeout(() => {
                window.print();
            }, 1000);
        },
        init() {
            if (this.playVoteCastedMessage) {
                tts(this.voteCastedMessage);
            }

            if (this.autoPrint) {
                this.printBallot();
            }
        }
    }"
    @print-ballot="printBallot()"
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
</x-filament-panels::page>
