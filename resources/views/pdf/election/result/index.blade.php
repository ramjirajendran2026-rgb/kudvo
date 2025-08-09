@php
    use App\Enums\ResolutionChoice;
    use App\Models\Candidate;use App\Models\Election;use Illuminate\Support\Collection;use Illuminate\Support\Number;

    /** @var Election $election */
@endphp

<x-layouts.pdf :title="$election->name">
    <div
        class="flex min-h-screen flex-col items-center justify-center space-y-6 border mx-1 rounded-lg p-4"
    >
        <img
            alt="{{ $organisation->name }}'s logo"
            src="{{ $organisation->logo_url }}"
            class="h-24 w-auto rounded-lg"
        />

        <h1 class="text-center text-3xl font-bold">
            {{ $organisation->name }}
        </h1>

        <h2 class="text-center text-2xl font-bold">{{ $election->name }}</h2>

        <h3 class="text-center text-xl font-bold">
            @if($election->preference->booth_voting)
                Online Voting<br />
            @endif
            {{ $election->starts_at_local?->format('d M, Y h:i A (T)') }}
            to
            {{ $election->ends_at_local?->format('d M, Y h:i A (T)') }}

            @if($election->preference->booth_voting)
                <br />Booth Voting<br />
                {{ $election->booth_starts_at_local?->format('d M, Y h:i A (T)') }}
                to
                {{ $election->booth_ends_at_local?->format('d M, Y h:i A (T)') }}
            @endif
        </h3>

        <h4 class="text-center text-lg font-bold underline">Results</h4>

        <div class="prose max-w-none">{!! $election->description !!}</div>
    </div>

    <div class="px-1">
        @foreach ($election->positions as $position)
            @pageBreak
            <div>
                <h3 class="text-center text-xl font-bold">
                    {{ $position->name }}
                </h3>
                <h4 class="text-center mb-2">
                    {{ $position->quota }}
                    {{ str(string: 'Post')->plural(count: $position->quota) }}
                </h4>

                <div class="divide-y border rounded-lg">
                    @php
                        $candidates = $position->candidates
                            ->sortByDesc(
                                fn (Candidate $candidate) => $election->result?->meta
                                    ?->toCollection()
                                    ->when(
                                        filled($boothId ?? null),
                                        fn (Collection $collection) => $collection->where('key', "$candidate->uuid:booth:$boothId"),
                                        fn (Collection $collection) => $collection->where('key', "$candidate->uuid"),
                                    )
                                    ->first()?->value ?? 0
                            )
                    @endphp
                    @foreach ($candidates as $candidate)
                        <div class="flex justify-between items-center gap-2 p-2 break-inside-avoid-page">
                            <div class="size-20 bg-black text-white text-3xl font-bold font-mono rounded-lg flex items-center justify-center">
                                {{ $candidate->sort }}
                            </div>
                            <div class="flex-1">
                                <div class="text-xl font-bold">{{ $candidate->full_name }}</div>
                                <div class="text-gray-800">
                                    @php
                                        $contacts = Arr::where(
                                            $candidate->only(['membership_number', 'phone', 'email']),
                                            fn ($item) => filled($item),
                                        );
                                    @endphp

                                    {{ implode(' • ', $contacts) }}
                                </div>
                            </div>
                            <div class="text-end text-xl font-bold font-mono text-white bg-black p-2 rounded-lg">
                                @if ($election->preference->disable_unopposed_selection && $position->isUnopposed())
                                    Unopposed
                                @else
                                    @php
                                        $votes =
                                            $election->result?->meta
                                                ?->toCollection()
                                                ->when(
                                                    filled($boothId ?? null),
                                                    fn (Collection $collection) => $collection->where('key', "$candidate->uuid:booth:$boothId"),
                                                    fn (Collection $collection) => $collection->where('key', "$candidate->uuid"),
                                                )
                                                ->first()?->value ?? 0;
                                    @endphp

                                    {{ str(string: "$votes vote")->plural($votes)->toString() }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.pdf>
