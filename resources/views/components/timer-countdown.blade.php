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
                ? (days > 0 ? '<span class=\'text-primary-500\'>'+days+'</span>d:' : '')
                + '<span class=\'text-primary-500\'>' + new Date(difference).toISOString().substring(11, 13) + '</span>h:'
                + '<span class=\'text-primary-500\'>' + new Date(difference).toISOString().substring(14, 16) + '</span>m:'
                + '<span class=\'text-primary-500\'>' + new Date(difference).toISOString().substring(17, 19) + '</span>s'
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
            'flex w-full flex-col items-center justify-center',
        ])
    }}
>
    <span x-text="label" class="text-gray-500 dark:text-gray-400"></span>
    <span x-html="remaining" class="font-mono text-xl font-semibold sm:text-3xl"></span>
</div>
