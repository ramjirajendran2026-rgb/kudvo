<x-filament-panels::page>
    @if (filled($pendingStep = $this->getPendingStep()))
        <x-filament.election.setup-steps
            :pending-step="$pendingStep"
            :current-step="$this->getCurrentStep()"
        />
    @endif

    @if ($this->hasReadAccess())
        @if ($this->shouldShowPricingTable())
            <div class="grid grid-cols-3 gap-6">
                @foreach ($this->getPlans() as $plan)
                    <div class="rounded-xl border p-6">
                        <h3 class="text-lg font-semibold">
                            {{ $plan->name }}
                        </h3>
                        <p class="mt-6 text-sm">
                            {{ $plan->description }}
                        </p>
                        <p class="mt-6">
                            <span class="text-4xl font-bold">
                                {{ money($plan->elector_fee, $plan->currency) }}
                            </span>
                            <span class="text-sm">/elector</span>
                        </p>
                        <div class="mt-6 flex justify-center">
                            {{ ($this->choosePlanAction)(['plan_id' => $plan->id]) }}
                        </div>
                        <ul role="list" class="mt-6 space-y-2">
                            @foreach ($plan->selfFeatures() as $feature)
                                <li class="flex items-start space-x-2">
                                    <x-heroicon-o-check-circle
                                        class="h-5 w-5 text-primary-500"
                                    />
                                    <span class="flex-1">
                                        {{ $feature->feature->getLabel() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <ul role="list" class="mt-6">
                            @foreach ($plan->addOnFeatures() as $feature)
                                <li class="flex items-center space-x-2">
                                    <x-heroicon-o-check-circle
                                        class="h-5 w-5 text-primary-500"
                                    />
                                    <span>
                                        {{ $feature->feature->getLabel() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @else
            <x-filament-panels::form wire:submit="save">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                />
            </x-filament-panels::form>
        @endif
    @else
        <x-filament::section>
            <x-filament.state
                heading="Access Denied"
                description="You do not have permission to access this page."
                icon="heroicon-o-no-symbol"
            />
        </x-filament::section>
    @endif

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
