@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Collection;

    $state = $getState();

    if ($state instanceof Collection) {
        $state = $state->all();
    }

    $state = Arr::wrap($state);

    $limit = $getLimit();
    $limitedState = array_slice($state, 0, $limit);
    $isCircular = $isCircular();
    $isSquare = $isSquare();
    $isStacked = $isStacked();
    $overlap = $isStacked ? ($getOverlap() ?? 2) : null;
    $ring = $isStacked ? ($getRing() ?? 2) : null;
    $height = $getHeight() ?? ($isStacked ? '2rem' : '2.5rem');
    $width = $getWidth() ?? (($isCircular || $isSquare) ? $height : null);

    $stateCount = count($state);
    $limitedStateCount = count($limitedState);

    $defaultImageUrl = $getDefaultImageUrl();

    if ((! $limitedStateCount) && filled($defaultImageUrl)) {
        $limitedState = [null];

        $limitedStateCount = 1;
    }

    $ringClasses = Arr::toCssClasses([
        'ring-white dark:ring-gray-900',
        match ($ring) {
            0 => null,
            1 => 'ring-1',
            2 => 'ring-2',
            3 => 'ring',
            4 => 'ring-4',
            default => $ring,
        },
    ]);

    $hasLimitedRemainingText = $hasLimitedRemainingText() && ($limitedStateCount < $stateCount);
    $isLimitedRemainingTextSeparate = $isLimitedRemainingTextSeparate();

    $limitedRemainingTextSizeClasses = match ($getLimitedRemainingTextSize()) {
        'xs' => 'text-xs',
        'sm', null => 'text-sm',
        'base', 'md' => 'text-base',
        'lg' => 'text-lg',
        default => $size,
    };
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-image',
                'px-3 py-4' => ! $isInline(),
            ])
    }}
>
    @if ($limitedStateCount)
        <div class="flex items-center gap-x-2.5">
            <div
                @class([
                    'flex',
                    'flex-wrap' => $canWrap(),
                    match ($overlap) {
                        0 => null,
                        1 => '-space-x-1',
                        2 => '-space-x-2',
                        3 => '-space-x-3',
                        4 => '-space-x-4',
                        5 => '-space-x-5',
                        6 => '-space-x-6',
                        7 => '-space-x-7',
                        8 => '-space-x-8',
                        default => 'gap-1.5',
                    },
                ])
            >
                @foreach ($limitedState as $stateItem)
                    <img
                        src="{{ filled($stateItem) ? $getImageUrl($stateItem) : $defaultImageUrl }}"
                        @if (filled($stateItem) && filled($srcset = $getImageSrcset($stateItem)))
                            srcset="{{ $srcset }}"
                        @endif
                        {{
                            $getExtraImgAttributeBag()
                                ->class([
                                    'max-w-none object-cover object-center',
                                    'aspect-video rounded-xl',
                                    'rounded-full' => $isCircular,
                                    $ringClasses,
                                ])
                                ->style([
                                    "height: {$height}" => $height,
                                    "width: {$width}" => $width,
                                ])
                        }}
                    />
                @endforeach

                @if ($hasLimitedRemainingText && (! $isLimitedRemainingTextSeparate) && $isCircular)
                    <div
                        style="
                            @if ($height) height: {{ $height }}; @endif
                            @if ($width) width: {{ $width }}; @endif
                        "
                        @class([
                            'flex items-center justify-center bg-gray-100 font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                            'rounded-full' => $isCircular,
                            $limitedRemainingTextSizeClasses,
                            $ringClasses,
                        ])
                        @style([
                            "height: {$height}" => $height,
                            "width: {$width}" => $width,
                        ])
                    >
                        <span class="-ms-0.5">
                            +{{ $stateCount - $limitedStateCount }}
                        </span>
                    </div>
                @endif
            </div>

            @if ($hasLimitedRemainingText && ($isLimitedRemainingTextSeparate || (! $isCircular)))
                <div
                    @class([
                        'font-medium text-gray-500 dark:text-gray-400',
                        $limitedRemainingTextSizeClasses,
                    ])
                >
                    +{{ $stateCount - $limitedStateCount }}
                </div>
            @endif
        </div>
    @elseif (($placeholder = $getPlaceholder()) !== null)
        <x-filament-tables::columns.placeholder>
            {{ $placeholder }}
        </x-filament-tables::columns.placeholder>
    @endif
</div>
