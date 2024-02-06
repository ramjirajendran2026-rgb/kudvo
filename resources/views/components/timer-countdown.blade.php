@props([
    'target',
    'label' => null,
    'targetLabel' => null,
    'targetEvent' => null,
    'reload' => false,
])

<div
    x-data="{
        'target': @js($target * 1000),
        'label': @js($label),
        'targetLabel': @js($targetLabel),
        'targetEvent': @js($targetEvent),
        'reload': @js($reload),

        'now': Date.now(),
        'interval': null,
        get remaining() {
            const difference = this.target - Math.floor(this.now);
            const days = Math.floor(difference / 24 / 3600 / 1000);
            return difference > 0
                ? (days > 0 ? days+':' : '') + new Date(difference)
                .toISOString()
                .substring(11, 19)
                : this.targetLabel;
        },
        init() {
            this.interval = setInterval(() => {
                this.now = Date.now()

                if (this.now >= this.target && this.interval) {
                    clearInterval(this.interval)
                    this.interval = 0;

                    if (this.targetEvent) {
                        $dispatch(this.targetEvent)
                    }

                    if (this.reload) {
                        window.location.reload()
                    }
                }
            }, 1000)
        }
    }"
    x-init="init()"
    {{
        $attributes->class([
            'flex flex-col items-center justify-center w-full'
        ])
    }}
>
    <span x-text="label" class="text-gray-500 dark:text-gray-400"></span>
    <span x-text="remaining" class="text-xl sm:text-3xl font-semibold font-mono"></span>
</div>
