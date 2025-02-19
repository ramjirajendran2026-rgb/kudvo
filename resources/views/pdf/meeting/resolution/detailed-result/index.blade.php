@php
    use App\Enums\ResolutionChoice;
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

        <h4 class="text-center text-lg font-bold underline">
            Detailed Results
        </h4>

        <div class="prose max-w-none">{!! $meeting->description !!}</div>
    </div>

    @foreach ($meeting->resolutions as $resolution)
        @php
            $hasAbstain = $resolution->allow_abstain_votes;
            $colspan = $hasAbstain ? 4 : 3;
            $summary = [
                ResolutionChoice::For->value => 0,
                ResolutionChoice::Against->value => 0,
                ResolutionChoice::Abstain->value => 0,
            ];
        @endphp

        @pageBreak
        <table class="w-full border-collapse border border-gray-200">
            <thead>
                <tr>
                    <th
                        colspan="{{ $colspan }}"
                        class="border border-gray-200 p-2"
                    >
                        {{ $resolution->name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td
                        colspan="{{ $colspan }}"
                        class="border border-gray-200 p-2"
                    >
                        <div class="prose max-w-none">
                            {!! $resolution->description !!}
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w-[40%] border border-gray-200 p-2">
                        Participant
                    </th>
                    <th
                        class="{{ $hasAbstain ? 'w-[20%]' : 'w-[30%]' }} border border-gray-200 p-2"
                    >
                        {{ $resolution->for_label }}
                    </th>
                    <th
                        class="{{ $hasAbstain ? 'w-[20%]' : 'w-[30%]' }} border border-gray-200 p-2"
                    >
                        {{ $resolution->against_label }}
                    </th>
                    @if ($hasAbstain)
                        <th class="w-[20%] border border-gray-200 p-2">
                            {{ $resolution->abstain_label }}
                        </th>
                    @endif
                </tr>
                @foreach ($meeting->participants as $participant)
                    @php
                        $votes = $participant->votes->where('resolution_id', $resolution->getKey());
                    @endphp

                    <tr>
                        <td class="border border-gray-200 p-2">
                            {{ $participant->name }}
                        </td>
                        <th class="border border-gray-200 p-2">
                            {{ $votes->where('response', ResolutionChoice::For)->count() ? $participant->weightage : '-' }}
                        </th>
                        <th class="border border-gray-200 p-2">
                            {{ $votes->where('response', ResolutionChoice::Against)->count() ? $participant->weightage : '-' }}
                        </th>
                        @if ($hasAbstain)
                            <th class="border border-gray-200 p-2">
                                {{ $votes->where('response', ResolutionChoice::Abstain)->count() ? $participant->weightage : '-' }}
                            </th>
                        @endif
                    </tr>

                    @php
                        $summary[ResolutionChoice::For->value] += $votes->where('response', ResolutionChoice::For)->count() ? $participant->weightage : 0;
                        $summary[ResolutionChoice::Against->value] += $votes->where('response', ResolutionChoice::Against)->count() ? $participant->weightage : 0;
                        $summary[ResolutionChoice::Abstain->value] += $votes->where('response', ResolutionChoice::Abstain)->count() ? $participant->weightage : 0;
                    @endphp
                @endforeach

                <tr>
                    <td class="border border-gray-200 p-2 text-end">Total</td>
                    <th class="border border-gray-200 p-2">
                        {{ $summary[ResolutionChoice::For->value] }}
                        ({{ Number::percentage(($summary[ResolutionChoice::For->value] / $meeting->participants->sum('weightage')) * 100, 2) }})
                    </th>
                    <th class="border border-gray-200 p-2">
                        {{ $summary[ResolutionChoice::Against->value] }}
                        ({{ Number::percentage(($summary[ResolutionChoice::Against->value] / $meeting->participants->sum('weightage')) * 100, 2) }})
                    </th>
                    @if ($hasAbstain)
                        <th class="border border-gray-200 p-2">
                            {{ $summary[ResolutionChoice::Abstain->value] }}
                            ({{ Number::percentage(($summary[ResolutionChoice::Abstain->value] / $meeting->participants->sum('weightage')) * 100, 2) }})
                        </th>
                    @endif
                </tr>
            </tbody>
        </table>
    @endforeach
</x-layouts.pdf>
