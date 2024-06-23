@php
    $gridDirection = $getGridDirection() ?? "column";
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();

    $options = $getOptions();

    $maxItems = $getMaxItems();
    $isPreview = $isPreview();
@endphp

<div
    x-data="{
        checkedOptionsCount: 0,

        maxItems: @js($maxItems),

        group: 'all',

        areAllCheckboxesChecked: false,

        checkboxListOptions: Array.from(
            $root.querySelectorAll('.fi-fo-checkbox-list-option-label'),
        ),

        search: '',

        visibleCheckboxListOptions: [],

        checkIfAllCheckboxesAreChecked: function (event) {
            this.checkedOptionsCount = this.checkboxListOptions.filter(
                (checkboxLabel) =>
                    checkboxLabel.querySelector('input[type=checkbox]:checked'),
            ).length

            if (this.checkedOptionsCount > this.maxItems && event !== undefined) {
                event.target.checked = false

                event.target.dispatchEvent(new Event('change'))

                Swal.fire({
                    title: 'Maximum Selection Reached',
                    text:
                        'You can only select a maximum of ' +
                        this.maxItems +
                        ' candidates for this position. Please deselect a candidate before selecting another one.',
                    icon: 'warning',
                })

                return
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
            this.visibleCheckboxListOptions = this.checkboxListOptions
                .filter((checkboxListItem) => {
                    return (
                        this.group === 'all' ||
                        this.group === checkboxListItem.dataset.candidateGroup ||
                        (this.group === 'independent' &&
                            ! checkboxListItem.dataset.candidateGroup)
                    )
                })
                .filter((checkboxListItem) => {
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
                })
        },

        candidateGroupSelected: function (group) {
            if (this.checkedOptionsCount < 1 && group !== this.group) {
                this.group = group

                this.updateVisibleCheckboxListOptions()
                this.checkIfAllCheckboxesAreChecked()
            }
        },

        selectCandidateGroup: function (group) {
            this.group = group

            this.updateVisibleCheckboxListOptions()
            this.checkIfAllCheckboxesAreChecked()

            $dispatch('candidate-group-selected', { group: group })
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
            "fi-vote-picker",
            "fi-invalid [&_.fi-fo-field-wrp-error-message]:hidden" => $errors->has(
                $statePath,
            ),
        ])
    >
        <x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
            <div class="space-y-4">
                @if ($hasCandidateGroup())
                    <div
                        class="flex flex-wrap justify-center gap-4"
                        @candidate-group-selected.window="candidateGroupSelected($event.detail.group)"
                    >
                        @foreach ($getCandidateGroups() as $key => $group)
                            <button
                                type="button"
                                @click="selectCandidateGroup('{{ $key }}')"
                                @class([
                                    "fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset",
                                    "min-w-[theme(spacing.6)] px-2 py-1",
                                ])
                                :class="{
                                'bg-success-50 text-success-600 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30': group === '{{ $key }}',
                                'bg-info-50 text-info-600 ring-info-600/10 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/20': group !== '{{ $key }}'
                            }"
                            >
                                {{ $group }}
                            </button>
                        @endforeach
                    </div>
                @endif

                @if (! $isDisabled)
                    @if ($isSearchable)
                        <x-filament::input.wrapper
                            inline-prefix
                            prefix-icon="heroicon-m-magnifying-glass"
                            prefix-icon-alias="forms:components.checkbox-list.search-field"
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
                            wire:key="{{ $this->getId() }}.{{ $getStatePath() }}.{{ $field::class }}.actions"
                        >
                            <span
                                x-show="
                                    ! areAllCheckboxesChecked &&
                                        checkedOptionsCount < maxItems &&
                                        visibleCheckboxListOptions.length <= maxItems - checkedOptionsCount
                                "
                                x-on:click="toggleAllCheckboxes()"
                                wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.select_all"
                            >
                                {{ $getAction("selectAll") }}
                            </span>

                            <span
                                x-show="areAllCheckboxesChecked && visibleCheckboxListOptions.length > 0"
                                x-on:click="toggleAllCheckboxes()"
                                wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.deselect_all"
                            >
                                {{ $getAction("deselectAll") }}
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
                    :x-show="! $isPreview && $isSearchable ? 'visibleCheckboxListOptions.length' : null"
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes($attributes)
                            ->merge($getExtraAttributes(), escape: false)
                            ->class([
                                'fi-fo-checkbox-list',
                                '!mt-0' => $isBulkToggleable && count($getOptions()),
                            ])
                    "
                >
                    @foreach ($options as $value => $label)
                        <div
                            wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.options.{{ $value }}"
                            @if (! $isPreview)
                                x-show="
                                    (group === 'all' ||
                                        group === '{{ $getCandidateGroupId($value) }}' ||
                                        (group === 'independent' && ! '{{ $getCandidateGroupId($value) }}')) &&
                                        (@js(! $isSearchable) ||
                                            $el
                                                .querySelector('.fi-fo-checkbox-list-option-label')
                                                ?.innerText.toLowerCase()
                                                .includes(search.toLowerCase()) ||
                                            $el
                                                .querySelector('.fi-fo-checkbox-list-option-description')
                                                ?.innerText.toLowerCase()
                                                .includes(search.toLowerCase()))
                                "
                            @endif
                            @class([
                                "fi-vote-picker-item break-inside-avoid border-gray-200 dark:border-white/10",
                            ])
                        >
                            <label
                                data-candidate-group="{{ $getCandidateGroupId($value) }}"
                                @class([
                                    "fi-fo-checkbox-list-option-label group flex items-center gap-x-3 p-2 lg:p-4",
                                    "cursor-pointer lg:hover:bg-gray-100 dark:lg:hover:bg-white/5" => ! $isDisabled,
                                ])
                            >
                                <div class="checkbox-ctn relative">
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
                                    <svg
                                        class="absolute inset-0 top-2 hidden h-4 w-4 peer-checked:block"
                                        viewBox="0 0 16 16"
                                        fill="white"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z"
                                        />
                                    </svg>
                                </div>

                                @if ($hasSymbol())
                                    <div class="relative h-10 w-10 max-w-none rounded">
                                        <svg
                                            class="absolute inset-0 hidden rounded print:block"
                                            viewBox="0 0 100 100"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <rect width="100" height="100" fill="#000000" />
                                        </svg>

                                        <img
                                            src="{{ $getSymbolUrl($value) }}"
                                            alt="{{ $label }}'s symbol"
                                            class="img-symbol absolute group-has-[:checked]:!bg-primary-600 print:group-has-[:checked]:!bg-transparent"
                                        />
                                    </div>
                                @endif

                                @if ($hasPhoto())
                                    <img
                                        src="{{ $getPhotoUrl($value) }}"
                                        alt="{{ $label }}'s photo"
                                        class="h-10 w-10 max-w-none rounded-full object-cover object-center print:hidden"
                                    />
                                @endif

                                <div class="grid flex-1 text-sm leading-6">
                                    <span
                                        class="fi-fo-checkbox-list-option-label text-lg font-medium text-gray-950 dark:text-white"
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
                            </label>
                        </div>
                    @endforeach
                </x-filament::grid>

                @if (blank($options))
                    <div
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.empty"
                        class="fi-placeholder px-6 py-12 text-center text-base font-semibold leading-6 text-gray-950 dark:text-white"
                    >
                        {{ $getPlaceholder() }}
                    </div>
                @endif

                @if (! $isPreview)
                    <div
                        x-cloak
                        x-show="! visibleCheckboxListOptions.length"
                        class="px-6 py-12 text-center text-base font-semibold leading-6 text-gray-950 dark:text-white"
                    >
                        {{ $getNoSearchResultsMessage() }}
                    </div>
                @endif
            </div>
        </x-dynamic-component>
    </x-filament::section>
</div>
