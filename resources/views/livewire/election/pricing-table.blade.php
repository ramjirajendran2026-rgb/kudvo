<div class="grid gap-6 pt-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
  <div class="col-span-full flex items-center justify-center">
    <x-filament::tabs label="Currency">
      @foreach ($supportedCurrencies as $supportedCurrency)
        <x-filament::tabs.item
          :active="$supportedCurrency === $currency"
          wire:target="currency"
          wire:loading.attr="disabled"
          wire:loading.class="opacity-50 cursor-wait"
          x-on:click="$wire.set('currency', '{{ $supportedCurrency }}')"
        >
          {{ $supportedCurrency }}
        </x-filament::tabs.item>
      @endforeach
    </x-filament::tabs>
  </div>

  @foreach ($this->plans as $plan)
    <div
      wire:key="plan-{{ $plan->id }}"
      class="cursor-default space-y-4 rounded-3xl bg-white p-4 md:p-6 lg:p-8 shadow-md hover:translate-y-1 hover:ring-2 ring-primary-600 transition-transform duration-300"
    >
      <h4 class="text-xl font-semibold sm:text-2xl lg:text-3xl">
        {{ $plan->name }}
      </h4>
      <p class="text-gray-600 text-sm md:text-base lg:text-lg">
        {{ $plan->description }}
      </p>
      <div class="py-4">
        <span wire:loading wire:target="currency" class="text-primary-600">
          Calculating...
        </span>

        <div
          wire:loading.class="hidden"
          wire:target="currency"
          class="space-y-2"
        >
          <div class="flex items-end justify-center gap-1">
            <span
              class="font-mono text-2xl font-bold text-primary-600 sm:text-3xl lg:text-4xl"
            >
              @money($plan->elector_fee, $plan->currency)
            </span>
            <span class="text-sm md:text-base">/elector</span>
          </div>
          <div class="font-mono text-sm md:text-base text-primary-600">
            + @money($plan->base_fee, $plan->currency)
          </div>
        </div>
      </div>
      <ul x-data="{ showAddOns: false }" class="space-y-2 text-start">
        @foreach ($plan->selfFeatures() as $feature)
          @if (! $feature->show_in_pricing)
            @continue
          @endif

          <li class="flex items-center gap-2">
            <x-filament::icon
              icon="heroicon-o-check-circle"
              class="h-5 w-5 text-green-500"
            />
            <span class="text-sm md:text-base">
              {{ $feature->feature->getLabel() }}
            </span>
          </li>
        @endforeach

        @if ($plan->addOnFeatures()->isNotEmpty())
          <li
            @click="showAddOns = ! showAddOns"
            class="flex cursor-pointer items-center gap-2 text-primary-600"
          >
            <x-filament::icon
              x-show="! showAddOns"
              icon="heroicon-o-plus"
              class="h-5 w-5"
            />
            <x-filament::icon
              x-show="showAddOns"
              icon="heroicon-o-chevron-up"
              class="h-5 w-5"
            />
            <span class="font-semibold text-sm md:text-base">Add-ons</span>
            <hr class="flex-1" />
          </li>
          @foreach ($plan->addOnFeatures() as $feature)
            <li x-show="showAddOns" class="flex items-center gap-2">
              <x-filament::icon
                icon="heroicon-o-sparkles"
                class="h-5 w-5 text-green-500"
              />
              <span class="text-sm md:text-base">
                {{ $feature->feature->getLabel() }}
              </span>
            </li>
          @endforeach
        @endif
      </ul>
    </div>
  @endforeach
</div>
