@php
    use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

    $languageSwitch = LanguageSwitch::make();
    $locales = $languageSwitch->getLocales();
    $isCircular = $languageSwitch->isCircular();
    $isFlagsOnly = $languageSwitch->isFlagsOnly();
    $hasFlags = filled($languageSwitch->getFlags());
    $isVisibleOutsidePanels = $languageSwitch->isVisibleOutsidePanels();
    $outsidePanelsPlacement = $languageSwitch->getOutsidePanelPlacement()->value;
    $placement = match (true) {
        $outsidePanelsPlacement === 'top-center' && $isFlagsOnly => 'bottom',
        $outsidePanelsPlacement === 'bottom-center' && $isFlagsOnly => 'top',
        ! $isVisibleOutsidePanels && $isFlagsOnly => 'bottom',
        default => 'bottom-end',
    };
@endphp

<x-filament::dropdown
    :teleport="true"
    :placement="$placement"
    :width="$isFlagsOnly ? 'flags-only' : 'fls-dropdown-width'"
    @class([
        'fi-dropdown fi-user-menu',
        'hidden' => ! $languageSwitch->isVisibleInsidePanels(),
    ])
>
    <x-slot name="trigger">
        <div
            @class([
                'language-switch-trigger flex h-9 w-9 items-center justify-center bg-primary-500/10 text-primary-600',
                'rounded-full' => $isCircular,
                'rounded-lg' => ! $isCircular,
                'p-1 ring-2 ring-inset ring-gray-200 hover:ring-gray-300 dark:ring-gray-500 hover:dark:ring-gray-400' => $isFlagsOnly || $hasFlags,
            ])
            x-tooltip="{
                content: @js($languageSwitch->getLabel(app()->getLocale())),
                theme: $store.theme,
                placement: 'bottom',
            }"
        >
            @if ($isFlagsOnly || $hasFlags)
                <x-filament-language-switch::flag
                    :src="$languageSwitch->getFlag(app()->getLocale())"
                    :circular="$isCircular"
                    :alt="$languageSwitch->getLabel(app()->getLocale())"
                    :switch="true"
                />
            @else
                <span class="text-md font-semibold">
                    {{ $languageSwitch->getCharAvatar(app()->getLocale()) }}
                </span>
            @endif
        </div>
    </x-slot>

    <x-filament::dropdown.list @class(['!border-t-0 space-y-1 !p-2.5'])>
        @foreach ($locales as $locale)
            @if (! app()->isLocale($locale))
                <a
                    rel="alternate"
                    hreflang="{{ $locale }}"
                    href="{{ LaravelLocalization::getLocalizedURL($locale, null, [], true) }}"
                    @if ($isFlagsOnly)
                        x-tooltip="{
                            content: @js($languageSwitch->getLabel($locale)),
                            theme: $store.theme,
                            placement: 'right',
                        }"
                    @endif
                    @class([
                        'fi-dropdown-list-item fi-dropdown-list-item-color-gray flex w-full items-center whitespace-nowrap rounded-md outline-none transition-colors duration-75 hover:bg-gray-950/5 focus:bg-gray-950/5 disabled:pointer-events-none disabled:opacity-70 dark:hover:bg-white/5 dark:focus:bg-white/5',
                        'justify-center px-2 py-0.5' => $isFlagsOnly,
                        'justify-start space-x-2 p-1 rtl:space-x-reverse' => ! $isFlagsOnly,
                    ])
                >
                    @if ($isFlagsOnly)
                        <x-filament-language-switch::flag
                            :src="$languageSwitch->getFlag($locale)"
                            :circular="$isCircular"
                            :alt="$languageSwitch->getLabel($locale)"
                            class="h-7 w-7"
                        />
                    @else
                        @if ($hasFlags)
                            <x-filament-language-switch::flag
                                :src="$languageSwitch->getFlag($locale)"
                                :circular="$isCircular"
                                :alt="$languageSwitch->getLabel($locale)"
                                class="h-7 w-7"
                            />
                        @else
                            <span
                                @class([
                                    'flex h-7 w-7 flex-shrink-0 items-center justify-center bg-primary-500/10 p-2 text-xs font-semibold text-primary-600 group-hover:border group-hover:border-primary-500/10 group-hover:bg-white group-hover:text-primary-600 group-focus:text-white',
                                    'rounded-full' => $isCircular,
                                    'rounded-lg' => ! $isCircular,
                                ])
                            >
                                {{ $languageSwitch->getCharAvatar($locale) }}
                            </span>
                        @endif
                        <span
                            class="text-sm font-medium text-gray-600 hover:bg-transparent dark:text-gray-200"
                        >
                            {{ $languageSwitch->getLabel($locale) }}
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
