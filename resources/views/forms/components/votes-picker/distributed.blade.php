@php
    use Filament\Support\Enums\ActionSize;
    use Illuminate\View\ComponentAttributeBag;

    $gridDirection = $getGridDirection() ?? 'column';
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();

    $hasError = $errors->has($statePath);
    $hasGroups = $hasGroups();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <section
        x-data="{
            credit: @js($getWeightage()),
            statePath: '{{ $statePath }}',
            values: @js(array_values($getState())),
            get remaining() {
                return this.credit - this.values.reduce((a, b) => a + b, 0)
            },
            get usedPercentage() {
                return (this.values.reduce((a, b) => a + b, 0) / this.credit) * 100
            },
            clear() {
                this.values = Array(@js(count($getOptions()))).fill(null)
                $wire.set(this.statePath, {}, false)

                $root.classList.remove('invalid')
                $root.querySelector('.votes-picker-error-text')?.remove()
            },
            onChange(event, index) {
                $root.classList.remove('invalid')
                $root.querySelector('.votes-picker-error-text')?.remove()

                let targetId = event.target.id
                let newValue = parseFloat(event.target.value)
                if (isNaN(newValue) || newValue < 0) {
                    this.values[index] = null
                    event.target.value = null
                    $wire.set(this.statePath + '.' + targetId, null, false)
                    return
                }

                let totalExceptCurrent = this.values.reduce(
                    (a, b, i) => a + (i === index ? 0 : b),
                    0,
                )
                let totalWithNew = totalExceptCurrent + newValue

                if (totalWithNew - this.credit <= Number.EPSILON) {
                    this.values[index] = newValue
                    $wire.set(this.statePath + '.' + targetId, newValue, false)
                } else {
                    event.target.value = this.values[index]
                    $wire.set(
                        this.statePath + '.' + targetId,
                        this.values[index],
                        false,
                    )
                    Swal.fire({
                        title: 'Not allowed',
                        text:
                            'You can only assign a maximum of ' +
                            this.credit +
                            ' votes across all candidates for this position.',
                        icon: 'error',
                        confirmButtonText: 'Okay',
                    })
                }
            },
        }"
        @class([
            'votes-picker distributed',
            'mx-auto w-full max-w-screen-md',
            'invalid' => $hasError,
        ])
    >
        <header
            x-data="{ isSticky: false }"
            x-on:scroll.window="isSticky = window.scrollY == $el.offsetTop"
            x-bind:class="{
                'rounded-t-none shadow border-b border-gray-200 dark:border-white/10':
                    isSticky,
            }"
            @class([
                'fi-section-header',
                'sticky top-0 z-10 rounded-t bg-inherit',
            ])
        >
            <div class="flex items-center gap-3 px-4 pt-2.5">
                <div class="grid flex-1 gap-y-1">
                    <x-filament::section.heading class="text-center">
                        {{ $getHeading() }}
                    </x-filament::section.heading>

                    @if ($hasError)
                        <div
                            class="votes-picker-error-text text-center text-base text-danger-600 dark:text-danger-400"
                        >
                            {{ $errors->has($statePath) ? $errors->first($statePath) : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null) }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex items-center justify-between gap-2 px-4 pb-2.5">
                <div class="text-base text-info-500 dark:text-info-400">
                    <span x-text="remaining"></span>
                    of
                    <span x-text="credit"></span>
                    remaining
                </div>
                @if (! $isDisabled)
                    <x-filament::link
                        x-cloak
                        x-show="usedPercentage > 0"
                        x-on:click="clear"
                        color="danger"
                        class="cursor-pointer"
                    >
                        Reset
                    </x-filament::link>
                @endif
            </div>
            <div
                x-cloak
                x-show="usedPercentage > 0"
                class="h-1 w-full overflow-hidden bg-gray-200 dark:bg-white/10"
            >
                <div
                    class="h-full bg-success-600"
                    x-bind:style="{ width: usedPercentage + '%' }"
                ></div>
            </div>
        </header>
        <div
            @class([
                'fi-section-content-ctn',
                'border-t border-gray-200 dark:border-white/10',
            ])
        >
            <div
                @class([
                    'fi-section-content',
                ])
            >
                <div class="divide-y divide-gray-200 dark:divide-white/10">
                    @forelse ($getOptions() as $value => $label)
                        <div
                            wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.options.{{ $value }}"
                            @class([
                                'flex flex-col justify-center gap-2 p-4 sm:flex-row sm:items-center',
                                'hidden' => ($isDisabled || $isOptionDisabled($value, $label)) && ! ($getState()[$value] ?? null),
                            ])
                        >
                            <div class="flex flex-1 items-center gap-2">
                                @if ($shouldShowSymbol())
                                    {{ $getSymbolImg($value) }}
                                @endif

                                @if ($shouldShowPhoto())
                                    {{ $getPhotoImg($value) }}
                                @endif

                                <div class="grid grow">
                                    <span
                                        class="text-base font-semibold leading-6 text-gray-950 dark:text-white"
                                    >
                                        {{ $label }}
                                    </span>

                                    @if ($hasDescription($value))
                                        <p
                                            class="overflow-hidden break-words text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {{ $getDescription($value) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="rounded ring-1 ring-gray-950/5 dark:ring-white/10"
                            >
                                <x-filament::input
                                    :attributes="
                                        \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                                            ->merge([
                                                'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                                'placeholder' => '0',
                                                'type' => 'number',
                                                'min' => 0,
                                                'id' => $value,
                                                'x-bind:value' => 'values[' . Js::from($loop->index) . ']',
                                                'x-on:input' => 'onChange($event, ' . Js::from($loop->index) . ')',
                                                'inputmode' => 'numeric',
                                                'wire:loading.attr' => 'disabled',
                                            ], escape: false)
                                            ->class(['text-center !text-xl placeholder:text-xl align-middle ip'])
                                    "
                                />
                            </div>
                        </div>
                    @empty
                        <div
                            wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.empty"
                        ></div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-dynamic-component>
