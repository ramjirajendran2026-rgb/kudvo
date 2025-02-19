@php
    use App\Enums\ResolutionChoice;
    use Illuminate\Support\Number;

    $overallWeightage = $meeting->participants()->sum('weightage');
@endphp

<x-layouts.pdf :title="$meeting->name">
    <div
        class="flex min-h-screen flex-col items-center justify-center space-y-6 border p-4"
    >
        <img
            alt="{{ $organisation->name }}'s logo"
            src="{{ $organisation->logo_url }}"
            class="h-24 w-auto rounded"
        />

        <h1 class="text-center text-3xl font-bold">
            {{ $organisation->name }}
        </h1>

        <h2 class="text-center text-2xl font-bold">{{ $meeting->name }}</h2>

        <h3 class="text-center text-xl font-bold">
            {{ $meeting->voting_starts_at_local?->format('d M, Y h:i A (T)') }}
            to
            {{ $meeting->voting_ends_at_local?->format('d M, Y h:i A (T)') }}
        </h3>

        <h4 class="text-center text-lg font-bold underline">Results</h4>

        <div class="prose max-w-none">{!! $meeting->description !!}</div>
    </div>

    @foreach ($meeting->resolutions as $resolution)
        @php
            $votes = $resolutionVotes->where('resolution_id', $resolution->getKey());
            $hasAbstain = $resolution->allow_abstain_votes;
            $colspan = 3;
            $summary = [
                ResolutionChoice::For->value => $votes->where('response', ResolutionChoice::For)->sum('weightage'),
                ResolutionChoice::Against->value => $votes->where('response', ResolutionChoice::Against)->sum('weightage'),
                ResolutionChoice::Abstain->value => $votes->where('response', ResolutionChoice::Abstain)->sum('weightage'),
            ];
        @endphp

        @pageBreak
        <table class="w-full border-collapse border">
            <thead>
                <tr>
                    <th colspan="{{ $colspan }}" class="border p-2">
                        {{ $resolution->name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="{{ $colspan }}" class="border p-2">
                        <div class="prose max-w-none">
                            {!! $resolution->description !!}
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w-1/3 border p-2"></th>
                    <th class="w-1/3 border p-2">Weightage</th>
                    <th class="w-1/3 border p-2">Percentage</th>
                </tr>
                <tr>
                    <th class="border p-2">
                        {{ $resolution->for_label }}
                    </th>
                    <td class="border p-2 text-center">
                        {{ $summary[ResolutionChoice::For->value] }}
                    </td>
                    <td class="border p-2 text-center">
                        {{ Number::percentage(($summary[ResolutionChoice::For->value] / $overallWeightage) * 100, 2) }}
                    </td>
                </tr>
                <tr>
                    <th class="border p-2">
                        {{ $resolution->against_label }}
                    </th>
                    <td class="border p-2 text-center">
                        {{ $summary[ResolutionChoice::Against->value] }}
                    </td>
                    <td class="border p-2 text-center">
                        {{ Number::percentage(($summary[ResolutionChoice::Against->value] / $overallWeightage) * 100, 2) }}
                    </td>
                </tr>
                @if ($hasAbstain)
                    <tr>
                        <th class="border p-2">
                            {{ $resolution->abstain_label }}
                        </th>
                        <td class="border p-2 text-center">
                            {{ $summary[ResolutionChoice::Abstain->value] }}
                        </td>
                        <td class="border p-2 text-center">
                            {{ Number::percentage(($summary[ResolutionChoice::Abstain->value] / $overallWeightage) * 100, 2) }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</x-layouts.pdf>
