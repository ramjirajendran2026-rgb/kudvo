<x-filament-panels::page>
    <div class="mx-auto max-w-2xl sm:text-center">
        <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            {{ __('filament.user.election-resource.pages.plan.heading') }}
        </h2>
        <p class="mt-4 text-lg leading-8 text-gray-500 dark:text-gray-400">
            {{ __('filament.user.election-resource.pages.plan.description') }}
        </p>
    </div>

    <div
        x-data="{
            totalElectors: $wire.entangle('totalElectors').live,
            validateInput() {
                const value = parseInt(this.totalElectors, 10)
                if (isNaN(value) || value < 1 || value > 999999) {
                    this.totalElectors = 500
                }
            },
        }"
        class="flex items-center justify-center gap-4"
    >
        <x-filament::input.wrapper
            :inline-prefix="true"
            :inline-suffix="true"
            :prefix="__('filament.user.election-resource.pages.plan.total_electors_input.prefix')"
            :suffix="__('filament.user.election-resource.pages.plan.total_electors_input.suffix')"
            class="w-full max-w-60 !rounded-full"
        >
            <x-filament::input
                type="number"
                min="1"
                max="999999"
                step="1"
                x-model="totalElectors"
                @input="validateInput"
                class="text-center font-mono font-semibold"
            />
        </x-filament::input.wrapper>
    </div>

    <div class="grid gap-4 pt-6 text-center md:grid-cols-3 md:gap-6">
        @foreach ($this->getPlans() as $plan)
            <div
                wire:key="plan-{{ $plan->id }}"
                class="{{ $activePlanId === $plan->id ? 'ring-1' : '' }} flex cursor-default flex-col gap-8 rounded-3xl bg-white p-6 shadow-sm ring-primary-600 hover:translate-y-1 hover:ring-2 dark:bg-gray-900 md:p-8"
            >
                <h5 class="sticky top-16 bg-white text-2xl font-semibold dark:bg-gray-900 md:text-3xl">
                    {{ $plan->name }}
                </h5>
                <p class="text-gray-600 dark:text-gray-50">
                    {{ $plan->description }}
                </p>
                <div class="space-y-1">
                    <span wire:loading wire:target="totalElectors,currency">Calculating...</span>

                    <div class="flex items-end justify-center gap-1">
                        <span
                            wire:loading.class="hidden"
                            wire:target="totalElectors,currency"
                            class="font-mono text-xl font-bold text-primary-600 dark:text-primary-500 md:text-2xl"
                        >
                            @money($plan->elector_fee * $totalElectors + $plan->base_fee, $plan->currency)
                        </span>
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
                                <x-filament::icon icon="heroicon-o-sparkles" class="h-6 w-6 text-primary-500" />
                                <span>
                                    {{ $feature->feature->getLabel() }}
                                </span>
                            </li>
                        @endforeach
                    @endif
                </ul>

                <div class="flex flex-1 items-end justify-center">
                    @if ($activePlanId === $plan->id)
                        <x-filament::button :disabled="true" class="w-full">Selected</x-filament::button>
                    @else
                        <div class="w-full">
                            {{ ($this->choosePlanAction)(['plan_id' => $plan->getKey()]) }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
