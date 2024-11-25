<?php

namespace App\Filament\User\Resources\ElectionResource\Widgets;

use App\Enums\ElectionResultSortBy;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CandidateVotesChart extends ChartWidget
{
    protected static ?string $pollingInterval = null;

    public Election $election;

    public Position $position;

    public ?ElectionResultSortBy $sortBy = ElectionResultSortBy::HighestVotes;

    public ?int $boothId = null;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Votes',
                    'data' => $this->getCandidates()
                        ->map(fn (Candidate $candidate): int => $this->getCandidateVotes($candidate->uuid))
                        ->values(),
                    'backgroundColor' => $this->getCandidates()
                        ->map(fn (Candidate $candidate): string => 'rgb(' . Color::rgb(sprintf('rgb(%u, %u, %u)', rand(0, 255), rand(0, 255), rand(0, 255)))['400'] . ')')
                        ->values(),
                ],
            ],
            'labels' => $this->getCandidates()
                ->map(fn (Candidate $candidate): string => $candidate->display_name)
                ->values(),
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getOptions(): array | RawJs | null
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'r' => [
                    'ticks' => [
                        'display' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->getPosition()->name;
    }

    public function getDescription(): string | Htmlable | null
    {
        return sprintf(
            '%s %s',
            $this->getPosition()->quota,
            Str::plural('Post', $this->getPosition()->quota)
        );
    }

    public function getElection(): Election
    {
        return $this->election;
    }

    protected function getPosition(): Position
    {
        return $this->position;
    }

    protected function getCandidates(): Collection
    {
        return $this->getPosition()
            ->candidates
            ->when(
                $this->getSortBy(),
                fn (\Illuminate\Database\Eloquent\Collection $collection, $sortBy) => $collection
                    ->sortBy(
                        fn (Candidate $candidate) => $this->getCandidateVotes($candidate->uuid),
                        descending: $sortBy === ElectionResultSortBy::HighestVotes
                    )
            );
    }

    public function getBoothId(): ?int
    {
        return $this->boothId;
    }

    public function getSortBy(): ?ElectionResultSortBy
    {
        return $this->sortBy;
    }

    protected function getCandidateVotes(string $key): int
    {
        $boothId = $this->getBoothId();

        return $this->getElection()->result?->meta->toCollection()
            ->when(
                filled($boothId),
                fn (Collection $collection) => $collection->where('key', "$key:booth:$boothId"),
                fn (Collection $collection) => $collection->where('key', "$key"),
            )
            ->first()
            ?->value ?? 0;
    }
}
