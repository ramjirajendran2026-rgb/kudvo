@php use App\Enums\ResolutionChoice; @endphp
<x-layouts.pdf :title="$meeting->name">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-center">{{ $organisation->name }}</h1>

        <h2 class="text-xl font-bold text-center">{{ $meeting->name }}</h2>

        <h3 class="text-lg font-bold text-center underline">Detailed Results</h3>

        <div class="prose max-w-none">{!! $meeting->description !!}</div>

        @foreach($meeting->resolutions as $resolution)
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
                    <th colspan="{{ $colspan }}" class="p-2 border border-gray-200">{{ $resolution->name }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="{{ $colspan }}" class="p-2 border border-gray-200">
                        <div class="prose max-w-none">{!! $resolution->description !!}</div>
                    </td>
                </tr>
                <tr>
                    <th class="p-2 border border-gray-200 w-[40%]">Participant</th>
                    <th class="p-2 border border-gray-200 {{ $hasAbstain ? 'w-[20%]' : 'w-[30%]' }}">{{ $resolution->for_label }}</th>
                    <th class="p-2 border border-gray-200 {{ $hasAbstain ? 'w-[20%]' : 'w-[30%]' }}">{{ $resolution->against_label }}</th>
                    @if($hasAbstain)
                        <th class="p-2 border border-gray-200 w-[20%]">{{ $resolution->abstain_label }}</th>
                    @endif
                </tr>
                @foreach($meeting->participants as $participant)
                    @php
                        $votes = $participant->votes->where('resolution_id', $resolution->getKey());
                    @endphp
                    <tr>
                        <td class="p-2 border border-gray-200">{{ $participant->name }}</td>
                        <th class="p-2 border border-gray-200">{{ $votes->where('response', ResolutionChoice::For)->count() ? $participant->weightage : '-' }}</th>
                        <th class="p-2 border border-gray-200">{{ $votes->where('response', ResolutionChoice::Against)->count() ? $participant->weightage : '-' }}</th>
                        @if($hasAbstain)
                            <th class="p-2 border border-gray-200">{{ $votes->where('response', ResolutionChoice::Abstain)->count() ? $participant->weightage : '-' }}</th>
                        @endif
                    </tr>

                    @php
                        $summary[ResolutionChoice::For->value] += $votes->where('response', ResolutionChoice::For)->count() ? $participant->weightage : 0;
                        $summary[ResolutionChoice::Against->value] += $votes->where('response', ResolutionChoice::Against)->count() ? $participant->weightage : 0;
                        $summary[ResolutionChoice::Abstain->value] += $votes->where('response', ResolutionChoice::Abstain)->count() ? $participant->weightage : 0;
                    @endphp
                @endforeach
                <tr>
                    <td class="p-2 border border-gray-200 text-end">Total</td>
                    <th class="p-2 border border-gray-200">{{ $summary[ResolutionChoice::For->value] }} ({{ \Illuminate\Support\Number::percentage(($summary[ResolutionChoice::For->value] / $meeting->participants->sum('weightage')) * 100, 2) }})</th>
                    <th class="p-2 border border-gray-200">{{ $summary[ResolutionChoice::Against->value] }} ({{ \Illuminate\Support\Number::percentage(($summary[ResolutionChoice::Against->value] / $meeting->participants->sum('weightage')) * 100, 2) }})</th>
                    @if($hasAbstain)
                        <th class="p-2 border border-gray-200">{{ $summary[ResolutionChoice::Abstain->value] }} ({{ \Illuminate\Support\Number::percentage(($summary[ResolutionChoice::Abstain->value] / $meeting->participants->sum('weightage')) * 100, 2) }})</th>
                    @endif
                </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</x-layouts.pdf>
