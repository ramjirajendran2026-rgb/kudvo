<div class="grid gap-4 pt-6 md:grid-cols-3 md:gap-6">
    <div class="col-span-full flex items-center justify-center">
        <x-filament::tabs label="Currency">
            @foreach ($supportedCurrencies as $supportedCurrency)
                <x-filament::tabs.item
                    :active="$supportedCurrency === $currency"
                    wire:click="setCurrency('{{ $supportedCurrency }}')"
                >
                    {{ $supportedCurrency }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
    </div>
    @foreach ($this->plans as $plan)
        <div
            wire:key="plan-{{ $plan->id }}"
            class="cursor-default space-y-4 rounded-3xl bg-white p-6 ring-primary-600 hover:translate-y-1 hover:ring-2 md:p-8"
        >
            <h5 class="text-2xl font-semibold md:text-3xl">
                {{ $plan->name }}
            </h5>
            <p class="text-gray-600">
                {{ $plan->description }}
            </p>
            <div class="space-y-1 py-6">
                <div class="flex items-end justify-center gap-1">
                    <span class="font-mono text-3xl font-bold md:text-4xl">
                        @money($plan->elector_fee, $plan->currency)
                    </span>
                    <span>/elector</span>
                </div>
                <div class="font-mono">
                    +
                    @money($plan->base_fee, $plan->currency)
                </div>
            </div>
            <ul x-data="{ showAddOns: false }" class="space-y-2 text-start">
                @foreach ($plan->selfFeatures() as $feature)
                    @if (! $feature->show_in_pricing)
                        @continue
                    @endif

                    <li class="flex gap-2">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-6 w-6 text-green-500" />
                        <span>
                            {{ $feature->feature->getLabel() }}
                        </span>
                    </li>
                @endforeach

                @if ($plan->addOnFeatures()->isNotEmpty())
                    <li
                        @click="showAddOns = ! showAddOns"
                        class="flex cursor-pointer items-center gap-2 text-primary-600"
                    >
                        <x-filament::icon x-show="! showAddOns" icon="heroicon-o-plus" class="h-6 w-6" />
                        <x-filament::icon x-show="showAddOns" icon="heroicon-o-chevron-up" class="h-6 w-6" />
                        <span class="font-semibold">Add-ons</span>
                        <hr class="flex-1" />
                    </li>
                    @foreach ($plan->addOnFeatures() as $feature)
                        <li x-show="showAddOns" class="flex gap-2">
                            <x-filament::icon icon="heroicon-o-sparkles" class="h-6 w-6 text-green-500" />
                            <span>
                                {{ $feature->feature->getLabel() }}
                            </span>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endforeach
</div>
