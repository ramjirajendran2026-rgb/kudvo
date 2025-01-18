<x-filament-widgets::widget>
    <ol
        role="list"
        @class([
            'fi-fo-wizard-header grid divide-y divide-gray-200 dark:divide-white/5 md:grid-flow-col md:divide-y-0 md:overflow-x-auto',
            'rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
        ])
    >
        @foreach ($this->getSteps() as $step)
            <li class="relative flex">
                <a
                    {{ \Filament\Support\generate_href_html($this->getStepUrl($step) ?? '#') }}
                    @class([
                        'flex h-full items-center gap-x-4 px-6 py-4 text-start',
                        '' => $pendingStep->getIndex() < $step->getIndex(),
                    ])
                >
                    <div
                        @class([
                            'flex h-10 w-10 shrink-0 items-center justify-center rounded-full',
                            'bg-primary-600 dark:bg-primary-500' => $currentStep->getIndex() > $step->getIndex(),
                            'border-2' => $currentStep->getIndex() <= $step->getIndex(),
                            'border-primary-600 bg-primary-600/20 dark:border-primary-500 dark:bg-primary-500/20' => $currentStep->getIndex() === $step->getIndex(),
                            'border-gray-300 dark:border-gray-600' => $currentStep->getIndex() < $step->getIndex(),
                        ])
                    >
                        @if ($currentStep->getIndex() > $step->getIndex())
                            <x-filament::icon
                                :alias="'forms::components.wizard.completed-step'"
                                :icon="'heroicon-o-check'"
                                class="h-6 w-6 text-white"
                            />
                        @elseif (filled($icon = $step->getIcon()))
                            <x-filament::icon
                                :icon="$icon"
                                @class([
                                    'h-6 w-6',
                                    'text-gray-500 dark:text-gray-400' => $currentStep->getIndex() !== $step->getIndex(),
                                    'text-primary-600 dark:text-primary-500' => $currentStep->getIndex() === $step->getIndex(),
                                ])
                            />
                        @else
                            <span
                                @class([
                                    'text-sm font-medium',
                                    'text-gray-500 dark:text-gray-400' => $currentStep->getIndex() !== $step->getIndex(),
                                    'text-primary-600 dark:text-primary-500' => $currentStep->getIndex() === $step->getIndex(),
                                ])
                            >
                                {{ $step->getIndex() }}
                            </span>
                        @endif
                    </div>

                    <div class="grid justify-items-start md:w-max md:max-w-60">
                        <span
                            @class([
                                'text-sm font-medium',
                                'text-gray-500 dark:text-gray-400' => $currentStep->getIndex() < $step->getIndex(),
                                'text-primary-600 dark:text-primary-400' => $currentStep->getIndex() === $step->getIndex(),
                                'text-gray-950 dark:text-white' => $currentStep->getIndex() > $step->getIndex(),
                            ])
                        >
                            {{ $step->getLabel() }}
                        </span>

                        @if (filled($description = $step->getDescription()))
                            <span
                                class="text-start text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ $description }}
                            </span>
                        @endif
                    </div>
                </a>

                @if (! $loop->last)
                    <div
                        aria-hidden="true"
                        class="absolute end-0 hidden h-full w-5 md:block"
                    >
                        <svg
                            fill="none"
                            preserveAspectRatio="none"
                            viewBox="0 0 22 80"
                            class="h-full w-full text-gray-200 dark:text-white/5 rtl:rotate-180"
                        >
                            <path
                                d="M0 -2L20 40L0 82"
                                stroke-linejoin="round"
                                stroke="currentcolor"
                                vector-effect="non-scaling-stroke"
                            ></path>
                        </svg>
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
</x-filament-widgets::widget>
