@php
    $gridDirection = $getGridDirection() ?? 'column';
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();

    $hasError = $errors->has($statePath);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <section
        x-data="{
            areAllCheckboxesChecked: false,

            checkboxListOptions: Array.from(
                $root.querySelectorAll('.votes-picker-option'),
            ),

            checkedOptionsCount: 0,

            search: '',

            visibleCheckboxListOptions: [],

            checkIfAllCheckboxesAreChecked: function (event) {
                this.checkedOptionsCount = this.checkboxListOptions.filter(
                    (checkboxLabel) =>
                        checkboxLabel.querySelector('input[type=checkbox]:checked'),
                ).length

                if (
                    this.checkedOptionsCount > $root.dataset.quota &&
                    event !== undefined
                ) {
                    event.target.checked = false

                    event.target.dispatchEvent(new Event('change'))

                    Swal.fire({
                        title: 'Maximum Selection Reached',
                        text:
                            'You can only select a maximum of ' +
                            $root.dataset.quota +
                            ' candidates for this position. Please deselect a candidate before selecting another one.',
                        icon: 'warning',
                    })

                    return
                }

                if (event !== undefined) {
                    $root.classList.remove('invalid')
                    $root.querySelector('.votes-picker-error-text')?.remove()
                }

                this.areAllCheckboxesChecked =
                    this.visibleCheckboxListOptions.length ===
                    this.visibleCheckboxListOptions.filter((checkboxLabel) =>
                        checkboxLabel.querySelector('input[type=checkbox]:checked'),
                    ).length
            },

            toggleAllCheckboxes: function () {
                let state = ! this.areAllCheckboxesChecked

                this.visibleCheckboxListOptions.forEach((checkboxLabel) => {
                    let checkbox = checkboxLabel.querySelector('input[type=checkbox]')

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
                                .querySelector('.votes-picker-option-label')
                                ?.innerText.toLowerCase()
                                .includes(this.search.toLowerCase())
                        ) {
                            return true
                        }

                        return checkboxListItem
                            .querySelector('.votes-picker-option-description')
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
                            $root.querySelectorAll('.votes-picker-option'),
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
        data-quota="{{ $getMaxItems() }}"
        @class([
            'votes-picker',
            'invalid' => $hasError,
        ])
    >
        <div
            x-data="{ isSticky: false }"
            x-on:scroll.window="isSticky = window.scrollY == $el.offsetTop"
            x-bind:class="{ 'sticked': isSticky }"
            class="votes-picker-header"
        >
            <h3 class="votes-picker-heading">
                {{ $getHeading() }}
            </h3>
            <div class="votes-picker-subheading">
                <div class="votes-picker-subheading-text">
                    {{ $getSubheading() }}
                </div>
                <div>•</div>
                <div class="votes-picker-subheading-counter">
                    <span x-text="checkedOptionsCount"></span>
                    <span>selected</span>
                </div>
            </div>
            @if ($hasError)
                <div class="votes-picker-error-text">
                    {{ $errors->has($statePath) ? $errors->first($statePath) : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null) }}
                </div>
            @endif
        </div>

        <div class="votes-picker-ctn">
            @if (! $isDisabled)
                @if ($isSearchable)
                    <x-filament::input.wrapper
                        :inline-prefix="true"
                        prefix-icon="heroicon-m-magnifying-glass"
                        prefix-icon-alias="forms:components.checkbox-list.search-field"
                    >
                        <x-filament::input
                            :inline-prefix="true"
                            :placeholder="$getSearchPrompt()"
                            type="search"
                            :attributes="
                                \Filament\Support\prepare_inherited_attributes(
                                    new ComponentAttributeBag([
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
                        wire:key="{{ $this->getId() }}.{{ $getStatePath() }}.{{ $field::class }}.actions"
                    >
                        <span
                            x-show="! areAllCheckboxesChecked"
                            x-on:click="toggleAllCheckboxes()"
                            wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.select-all"
                        >
                            {{ $getAction('selectAll') }}
                        </span>

                        <span
                            x-show="areAllCheckboxesChecked"
                            x-on:click="toggleAllCheckboxes()"
                            wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.deselect-all"
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
                            'votes-picker-list',
                            '-mt-4' => $gridDirection === 'column',
                        ])
                "
            >
                @forelse ($getOptions() as $value => $label)
                    <label
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.options.{{ $value }}"
                        @if ($isSearchable && ! $isDisabled && ! $isOptionDisabled($value, $label))
                            x-show="
                                $el
                                    .querySelector('.votes-picker-option-label')
                                    ?.innerText.toLowerCase()
                                    .includes(search.toLowerCase()) ||
                                    $el
                                        .querySelector('.votes-picker-option-description')
                                        ?.innerText.toLowerCase()
                                        .includes(search.toLowerCase())
                            "
                        @endif
                        @class([
                            'votes-picker-option group',
                            'break-inside-avoid pt-4' => $gridDirection === 'column',
                        ])
                    >
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
                                    ->class(['votes-picker-option-cb'])
                            "
                        />

                        @if ($shouldShowSymbol())
                            {{ $getSymbolImg($value) }}
                        @endif

                        @if ($shouldShowPhoto())
                            {{ $getPhotoImg($value) }}
                        @endif

                        <div class="grid grow text-sm leading-6">
                            <span class="votes-picker-option-label">
                                {{ $label }}
                            </span>

                            @if ($hasDescription($value))
                                <p class="votes-picker-option-description">
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>
                    </label>
                @empty
                    <div
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.empty"
                    ></div>
                @endforelse
            </x-filament::grid>

            @if ($isSearchable)
                <div
                    x-cloak
                    x-show="search && ! visibleCheckboxListOptions.length"
                    class="votes-picker-no-search-results-message text-sm text-gray-500 dark:text-gray-400"
                >
                    {{ $getNoSearchResultsMessage() }}
                </div>
            @endif
        </div>
    </section>
</x-dynamic-component>
