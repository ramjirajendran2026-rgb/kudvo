@php
    use App\Facades\Kudvo;
    use Illuminate\Support\Js;
@endphp

<footer {{ $attributes->class(['fi-footer']) }}>
    <div>
        <div>
            @if (Kudvo::isBoothDevice())
                <span
                    x-data
                    x-tooltip="{{ Js::encode(['content' => Kudvo::getElectionBoothToken()->name]) }}"
                >
                    Booth
                </span>
                Voting by
            @else
                    Powered by
            @endif
            <x-filament::link target="_blank" :href="route('home')">
                {{ config('app.name') }}
            </x-filament::link>
        </div>
    </div>
</footer>
