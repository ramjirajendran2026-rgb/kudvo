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
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-image w-full',
                'px-3 py-4' => ! $isInline(),
            ])
    }}
>
    @if ($limitedStateCount)
        @php
            $img = $getRecord()->getFirstMedia('cover')
                ?->img(extraAttributes: [
                    'alt' => $getRecord()->title,
                    'class' => 'aspect-video rounded-lg max-w-none object-cover object-center w-full'
                ])
        @endphp

        @if($img)
            {{ $img }}
        @else
            <img
                src="{{ $defaultImageUrl }}"
                alt="{{ $getRecord()->title }}"
                class="aspect-video rounded-lg max-w-none object-cover object-center w-full"
            />
        @endif
    @elseif (($placeholder = $getPlaceholder()) !== null)
        <x-filament-tables::columns.placeholder>
            {{ $placeholder }}
        </x-filament-tables::columns.placeholder>
    @endif
</div>
