@php
    $gridDirection = $getGridDirection() ?? 'column';
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();

    $options = $getOptions();

    $maxItems = $getMaxItems();
@endphp

<div
    x-data="{
        checkedOptionsCount: 0,

        maxItems: @js($maxItems),

        areAllCheckboxesChecked: false,

        checkboxListOptions: Array.from(
            $root.querySelectorAll('.fi-fo-checkbox-list-option-label'),
        ),

        search: '',

        visibleCheckboxListOptions: [],

        checkIfAllCheckboxesAreChecked: function (event) {
            this.checkedOptionsCount = this.checkboxListOptions.filter((checkboxLabel) =>
                    checkboxLabel.querySelector('input[type=checkbox]:checked'),
                ).length

            if (this.checkedOptionsCount > this.maxItems && event !== undefined) {
                event.target.checked = false;

                event.target.dispatchEvent(new Event('change'));

                new FilamentNotification()
                    .title('Maximum Selection Reached')
                    .body('You can only select a maximum of ' + this.maxItems + ' candidates for this position. Please deselect a candidate before selecting another one.')
                    .danger()
                    .send();

                return;
            }

            this.areAllCheckboxesChecked =
                this.visibleCheckboxListOptions.length ===
                this.visibleCheckboxListOptions.filter((checkboxLabel) =>
                    checkboxLabel.querySelector('input[type=checkbox]:checked'),
                ).length
        },

        toggleAllCheckboxes: function () {
            state = ! this.areAllCheckboxesChecked

            this.visibleCheckboxListOptions.forEach((checkboxLabel) => {
                checkbox = checkboxLabel.querySelector('input[type=checkbox]')

                checkbox.checked = state
                checkbox.dispatchEvent(new Event('change'))
            })

            this.areAllCheckboxesChecked = state
        },

        updateVisibleCheckboxListOptions: function () {
            this.visibleCheckboxListOptions = this.checkboxListOptions.filter(
                (checkboxListItem) => {
                    if (
                        checkboxListItem
                            .querySelector('.fi-fo-checkbox-list-option-label')
                            ?.innerText.toLowerCase()
                            .includes(this.search.toLowerCase())
                    ) {
                        return true
                    }

                    return checkboxListItem
                        .querySelector('.fi-fo-checkbox-list-option-description')
                        ?.innerText.toLowerCase()
                        .includes(this.search.toLowerCase())
                },
            )
        },
    }"
    x-init="
        updateVisibleCheckboxListOptions()

        $nextTick(() => {
            checkIfAllCheckboxesAreChecked()
        })

        Livewire.hook('commit', ({ component, commit, succeed, fail, respond }) => {
            succeed(({ snapshot, effect }) => {
                $nextTick(() => {
                    if (component.id !== @js($this->getId())) {
                        return
                    }

                    checkboxListOptions = Array.from(
                        $root.querySelectorAll('.fi-fo-checkbox-list-option-label'),
                    )

                    updateVisibleCheckboxListOptions()

                    checkIfAllCheckboxesAreChecked()
                })
            })
        })

        $watch('search', () => {
            updateVisibleCheckboxListOptions()
            checkIfAllCheckboxesAreChecked()
        })
    "
>
    <x-filament::section
        :compact="false"
        :heading="$getHeading()"
        :description="$getSectionDescription()"
        @class([
            'fi-vote-picker',
            'fi-invalid [&_.fi-fo-field-wrp-error-message]:hidden' => $errors->has($statePath)
        ])
    >
        <x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
            <div>
                @if (! $isDisabled)
                    @if ($isSearchable)
                        <x-filament::input.wrapper
                            inline-prefix
                            prefix-icon="heroicon-m-magnifying-glass"
                            prefix-icon-alias="forms:components.checkbox-list.search-field"
                            class="mb-4"
                        >
                            <x-filament::input
                                inline-prefix
                                :placeholder="$getSearchPrompt()"
                                type="search"
                                :attributes="
                            \Filament\Support\prepare_inherited_attributes(
                                new \Illuminate\View\ComponentAttributeBag([
                                    'x-model.debounce.' . $getSearchDebounce() => 'search',
                                ])
                            )
                        "
                            />
                        </x-filament::input.wrapper>
                    @endif

                    @if ($isBulkToggleable && count($getOptions()))
                        <div
                            x-cloak
                            class="mb-2"
                            wire:key="{{ $this->getId() }}.{{ $getStatePath() }}.{{ $field::class }}.actions"
                        >
                    <span
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.select_all"
                    >
                        {{ $getAction('selectAll') }}
                    </span>

                            <span
                                x-show="areAllCheckboxesChecked"
                                x-on:click="toggleAllCheckboxes()"
                                wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.deselect_all"
                            >
                        {{ $getAction('deselectAll') }}
                    </span>
                        </div>
                    @endif
                @endif

                <x-filament::grid
                    :default="$getColumns('default')"
                    :sm="$getColumns('sm')"
                    :md="$getColumns('md')"
                    :lg="$getColumns('lg')"
                    :xl="$getColumns('xl')"
                    :two-xl="$getColumns('2xl')"
                    :direction="$gridDirection"
                    :x-show="$isSearchable ? 'visibleCheckboxListOptions.length' : null"
                    :attributes="
                \Filament\Support\prepare_inherited_attributes($attributes)
                    ->merge($getExtraAttributes(), escape: false)
                    ->class([
                        'fi-fo-checkbox-list gap-0 divide-y',
                        '-my-4' => $gridDirection === 'column',
                    ])
            "
                >
                    @foreach ($options as $value => $label)
                        <div
                            wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.options.{{ $value }}"
                            @if ($isSearchable)
                                x-show="
                            $el
                                .querySelector('.fi-fo-checkbox-list-option-label')
                                ?.innerText.toLowerCase()
                                .includes(search.toLowerCase()) ||
                                $el
                                    .querySelector('.fi-fo-checkbox-list-option-description')
                                    ?.innerText.toLowerCase()
                                    .includes(search.toLowerCase())
                        "
                            @endif
                            @class([
                                'break-inside-avoid py-2 border-gray-200 dark:border-white/10' => $gridDirection === 'column',
                            ])
                        >
                            <label
                                @class([
                                    'fi-fo-checkbox-list-option-label flex items-center gap-x-3 py-2 rounded-xl',
                                    'cursor-pointer md:hover:bg-gray-100 md:hover:px-4 dark:md:hover:bg-white/5' => ! $isDisabled,
                                ])
                            >
                                <div class="relative">
                                    <x-filament::input.checkbox
                                        :valid="! $errors->has($statePath)"
                                        :attributes="
                                \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                                    ->merge([
                                        'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                        'value' => $value,
                                        'wire:loading.attr' => 'disabled',
                                        $applyStateBindingModifiers('wire:model') => $statePath,
                                        'x-on:change' => 'checkIfAllCheckboxesAreChecked($event)',
                                    ], escape: false)
                                    ->class(['mt-1 w-8 h-8 peer'])
                            "
                                    />
                                    <svg class="mt-1 w-8 h-8 absolute inset-0 hidden peer-checked:block" viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'>
                                        <path d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/>
                                    </svg>
                                </div>

                                @if($hasPhoto())
                                    <img
                                        src="{{ $getPhotoUrl($value) }}"
                                        alt="{{ $label }}'s photo"
                                        class="max-w-none object-cover object-center rounded-full w-10 h-10 md:w-20 md:h-20"
                                    />
                                @endif

                                <div class="grid flex-1 text-sm leading-6">
                            <span
                                class="fi-fo-checkbox-list-option-label font-medium text-lg text-gray-950 dark:text-white"
                            >
                                {{ $label }}
                            </span>

                                    @if ($hasDescription($value))
                                        <p
                                            class="fi-fo-checkbox-list-option-description text-gray-500 dark:text-gray-400"
                                        >
                                            {{ $getDescription($value) }}
                                        </p>
                                    @endif
                                </div>

                                @if($hasSymbol())
                                    <img
                                        src="{{ $getSymbolUrl($value) }}"
                                        alt="{{ $label }}'s symbol"
                                        class="max-w-none object-cover object-center rounded-xl w-10 h-10 md:w-20 md:h-20"
                                    />
                                @endif
                            </label>
                        </div>
                    @endforeach
                </x-filament::grid>

                @if(blank($options))
                    <div
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.empty"
                        class="text-center text-base font-semibold leading-6 text-gray-950 dark:text-white px-6 py-12"
                    >
                        {{ $getPlaceholder() }}
                    </div>
                @endif

                @if ($isSearchable)
                    <div
                        x-cloak
                        x-show="search && ! visibleCheckboxListOptions.length"
                        class="fi-fo-checkbox-list-no-search-results-message text-sm text-gray-500 dark:text-gray-400"
                    >
                        {{ $getNoSearchResultsMessage() }}
                    </div>
                @endif
            </div>
        </x-dynamic-component>
    </x-filament::section>
</div>
