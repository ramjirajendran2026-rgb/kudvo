<x-filament-panels::page>
    <x-filament::section>
        <div class="space-y-6 px-6 lg:px-8 py-12">
            <div class="mx-auto max-w-2xl sm:text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Choose a Plan
                </h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    Choose a plan that best suits your needs.
                </p>
            </div>
            @foreach($this->getPlans() as $plan)
                <div x-data="{more: false, addOnMore: false}" class="mx-auto max-w-2xl rounded-3xl ring-1 ring-gray-200 lg:mx-0 lg:flex lg:max-w-none">
                    <div class="px-8 sm:px-10 lg:flex-auto">
                        <div class="bg-white pt-8 sm:pt-10">
                            <h3 class="text-2xl font-bold tracking-tight text-gray-900">
                                {{ $plan->name }}
                            </h3>
                            <p class="mt-6 text-base leading-7 text-gray-600">
                                {{ $plan->description }}
                            </p>
                        </div>
                        <div class="pb-8 sm:pb-10">
                            <div @click="more = !more" class="sticky top-16 bg-white mt-10 flex items-center gap-x-4 cursor-pointer">
                                <h4 class="flex-none text-sm font-semibold leading-6 text-indigo-600">
                                    What’s included
                                </h4>
                                <div class="h-px flex-auto bg-gray-100"></div>
                                <span x-show="{{ \Illuminate\Support\Js::from($plan->selfFeatures()->count() > 6) }}">
                                    <svg x-show="!more" class="h-6 w-5 flex-none text-gray-600 hover:text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                    <svg x-show="more" class="h-6 w-5 flex-none text-gray-600 hover:text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                    </svg>
                                </span>
                            </div>
                            <ul role="list" class="mt-8 grid grid-cols-1 gap-4 text-sm leading-6 text-gray-600 sm:grid-cols-2 sm:gap-6">
                                @foreach($plan->selfFeatures()->take(6) as $feature)
                                    <li class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $feature->feature->getLabel() }}
                                    </li>
                                @endforeach
                                @foreach($plan->selfFeatures()->slice(6) as $feature)
                                    <template x-if="more">
                                        <li class="flex gap-x-3">
                                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $feature->feature->getLabel() }}
                                        </li>
                                    </template>
                                @endforeach
                            </ul>
                            @if($plan->addOnFeatures()->count())
                                <div @click="addOnMore = !addOnMore" class="sticky top-16 bg-white mt-10 flex items-center gap-x-4 cursor-pointer">
                                    <h4 class="flex-none text-sm font-semibold leading-6 text-indigo-600">
                                        Add-on features
                                    </h4>
                                    <div class="h-px flex-auto bg-gray-100"></div>
                                    <span x-show="{{ \Illuminate\Support\Js::from($plan->addOnFeatures()->count() > 4) }}">
                                        <svg x-show="!addOnMore" class="h-6 w-5 flex-none text-gray-600 hover:text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <svg x-show="addOnMore" class="h-6 w-5 flex-none text-gray-600 hover:text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </span>
                                </div>
                                <ul role="list" class="mt-8 grid grid-cols-1 gap-4 text-sm leading-6 text-gray-600 sm:grid-cols-2 sm:gap-6">
                                    @foreach($plan->addOnFeatures()->take(4) as $feature)
                                        <li class="flex gap-x-3">
                                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $feature->feature->getLabel() }}
                                        </li>
                                    @endforeach
                                    @foreach($plan->addOnFeatures()->slice(4) as $feature)
                                        <template x-if="addOnMore">
                                            <li class="flex gap-x-3">
                                                <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $feature->feature->getLabel() }}
                                            </li>
                                        </template>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                    <div class="-mt-2 p-2 lg:h-full lg:mt-0 lg:w-full lg:max-w-md lg:flex-shrink-0 sticky top-16">
                        <div class="rounded-2xl bg-gray-50 py-10 text-center ring-1 ring-inset ring-gray-900/5 lg:flex lg:flex-col lg:justify-center lg:py-16">
                            <div class="mx-auto max-w-xs px-8">
                                <p class="text-base font-semibold text-gray-600">

                                </p>
                                <p class="mt-6 flex items-baseline justify-center gap-x-2">
                                        <span class="text-5xl font-bold tracking-tight text-gray-900">
                                            {{ money($plan->elector_fee, $plan->currency) }}
                                        </span>
                                    <span class="text-sm font-semibold leading-6 tracking-wide text-gray-600">/elector</span>
                                </p>
                                @if($plan->base_fee)
                                    <p class="text-sm font-semibold leading-6 tracking-wide text-gray-600">+ {{ money($plan->base_fee, $plan->currency) }}</p>
                                @endif
                                <div class="mt-10">
                                    {{ ($this->choosePlanAction)(['plan_id' => $plan->getKey()]) }}
                                </div>
                                <p class="mt-6 text-xs leading-5 text-gray-600">Invoices and receipts available for easy company reimbursement</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
