<x-filament-panels::page>
    @foreach ($this->nominees as $nominee)
        <x-filament::section
            :compact="true"
            :heading="$nominee->position->name"
        >
            <ol
                @class([
                    'grid divide-y divide-gray-200 dark:divide-white/5',
                    'rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
                ])
            >
                @foreach ($nominee->nominators as $nominator)
                    <li class="relative flex">
                        <button
                            class="flex h-full w-full items-center gap-x-4 px-6 py-4"
                        >
                            <div
                                @class([
                                    'flex h-10 w-10 shrink-0 items-center justify-center rounded-full',
                                    'bg-primary-600 dark:bg-primary-500' => $nominator->isAccepted(),
                                    'bg-danger-600 dark:bg-danger-500' => $nominator->isDeclined(),
                                    'border-2 border-warning-600 dark:border-warning-500' => $nominator->isPending(),
                                ])
                            >
                                <x-filament::icon
                                    alias="forms::components.wizard.completed-step"
                                    :icon="$nominator->isPending() ? 'heroicon-o-shield-exclamation' : ($nominator->isAccepted() ? 'heroicon-o-hand-thumb-up' : 'heroicon-o-hand-thumb-down')"
                                    class="h-6 w-6 text-white"
                                    @class([
                                        'h-6 w-6',
                                        'text-white' => ! $nominator->isPending(),
                                        'text-warning-600 dark:text-warning-500' => $nominator->isPending(),
                                    ])
                                />
                            </div>
                        </button>
                    </li>
                @endforeach
            </ol>
        </x-filament::section>
    @endforeach
</x-filament-panels::page>
