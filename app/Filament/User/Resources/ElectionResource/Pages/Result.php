<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Actions\Election\GenerateResultPdf;
use App\Enums\ElectionResultSortBy;
use App\Filament\User\Resources\ElectionResource\Widgets\CandidateVotesChart;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Actions\SelectAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Result extends ElectionPage
{
    protected static string $view = 'filament.user.resources.election-resource.pages.result';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chart-pie';

    public ?ElectionResultSortBy $sortBy = ElectionResultSortBy::HighestVotes;

    public ?int $boothId = null;

    public bool $chartView = false;

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election) &&
            static::can(action: 'viewResult', election: $election);
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.result.navigation_label');
    }

    protected function getFooterWidgets(): array
    {
        if (! $this->chartView) {
            return [];
        }

        return $this->getElection()->positions
            ->mapWithKeys(fn (Position $position) => [Str::uuid()->toString() => CandidateVotesChart::make(['position' => $position])])
            ->toArray();
    }

    public function getWidgetData(): array
    {
        return [
            ...parent::getWidgetData(),
            'sortBy' => $this->sortBy,
            'boothId' => $this->boothId,
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(components: [
                RepeatableEntry::make(name: 'positions')
                    ->contained(condition: false)
                    ->extraAttributes(attributes: ['class' => 'position-repeatable-entry'])
                    ->hiddenLabel()
                    ->placeholder(placeholder: fn () => $this->generateEmptyStatePlaceholder(
                        heading: 'No positions',
                        icon: 'heroicon-o-x-mark',
                    ))
                    ->schema(components: [
                        Section::make(heading: fn (Position $state): ?string => $state->name)
                            ->collapsed()
                            ->compact()
                            ->description(description: fn (Position $state): ?string => $state->quota . Str::plural(value: ' Post', count: $state->quota))
                            ->schema(components: [
                                RepeatableEntry::make(name: 'candidates')
                                    ->extraAttributes(attributes: ['class' => 'candidate-repeatable-entry [&_.fi-fo-component-ctn]:gap-2'])
                                    ->hiddenLabel()
                                    ->placeholder(placeholder: $this->generateEmptyStatePlaceholder(
                                        heading: 'No candidates',
                                        description: 'Create new candidate',
                                        icon: 'heroicon-o-x-mark',
                                    ))
                                    ->getStateUsing(
                                        fn (Position $record) => $record
                                            ->candidates
                                            ->when(
                                                $this->sortBy,
                                                fn (\Illuminate\Database\Eloquent\Collection $collection, $sortBy) => $collection
                                                    ->sortBy(
                                                        fn (Candidate $candidate) => $this->getCandidateVotes($candidate->uuid, $this->boothId),
                                                        descending: $sortBy === ElectionResultSortBy::HighestVotes
                                                    )
                                            )
                                    )
                                    ->schema(components: [
                                        Split::make([
                                            Split::make(schema: [
                                                SpatieMediaLibraryImageEntry::make(name: 'symbol')
                                                    ->collection(collection: Candidate::MEDIA_COLLECTION_SYMBOL)
                                                    ->defaultImageUrl(url: fn (Candidate $record): ?string => $record->symbol_url)
                                                    ->extraImgAttributes(attributes: ['class' => 'rounded aspect-square bg-black size-8 md:size-12 lg:size-16'])
                                                    ->grow(condition: false)
                                                    ->size('')
                                                    ->hiddenLabel()
                                                    ->visible(condition: $this->getElection()->preference?->candidate_symbol),

                                                SpatieMediaLibraryImageEntry::make(name: 'photo')
                                                    ->circular()
                                                    ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
                                                    ->defaultImageUrl(url: fn (Candidate $record): ?string => $record->photo_url)
                                                    ->extraImgAttributes(['class' => 'aspect-square size-8 md:size-12 lg:size-16'])
                                                    ->grow(condition: false)
                                                    ->height('')
                                                    ->hiddenLabel()
                                                    ->visible(condition: $this->getElection()->preference?->candidate_photo),

                                                TextEntry::make(name: 'display_name')
                                                    ->extraAttributes(attributes: fn (Candidate $record): array => $record->disabled ? ['class' => 'line-through'] : [])
                                                    ->helperText(
                                                        text: fn (Candidate $record): ?string => collect(value: [
                                                            $record->membership_number,
                                                            $this->getElection()->preference->candidate_group ? $record->candidateGroup?->name : null,
                                                        ])
                                                            ->filter(callback: fn (?string $item): bool => filled($item))
                                                            ->implode(value: ' • ')
                                                    )
                                                    ->hiddenLabel()
                                                    ->size(size: TextEntry\TextEntrySize::Medium)
                                                    ->weight(weight: FontWeight::Medium),
                                            ]),

                                            TextEntry::make(name: 'unopposed')
                                                ->alignCenter()
                                                ->badge()
                                                ->color(color: 'primary')
                                                ->extraAttributes(attributes: ['class' => '[&_.fi-badge]:text-xl [&_.fi-badge]:w-full [&_.w-max:has(.fi-badge)]:w-full'])
                                                ->getStateUsing(callback: fn () => 'Unopposed')
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: TextEntry\TextEntrySize::Large)
                                                ->visible(condition: fn (Candidate $record): bool => $this->getElection()->preference->disable_unopposed_selection && $record->position->isUnopposed())
                                                ->weight(weight: FontWeight::Medium),

                                            TextEntry::make(name: 'votes')
                                                ->alignCenter()
                                                ->badge()
                                                ->color(color: 'success')
                                                ->extraAttributes(attributes: ['class' => '[&_.fi-badge]:text-xl [&_.fi-badge]:w-full [&_.w-max:has(.fi-badge)]:w-full'])
                                                ->formatStateUsing(callback: fn (string $state): string => Str::plural(value: "$state vote", count: $state))
                                                ->getStateUsing(callback: fn (Candidate $record) => $this->getCandidateVotes($record->uuid, $this->boothId))
                                                ->grow(condition: false)
                                                ->hidden(condition: fn (Candidate $record): bool => $this->getElection()->preference->disable_unopposed_selection && $record->position->isUnopposed())
                                                ->hiddenLabel()
                                                ->size(size: TextEntry\TextEntrySize::Large)
                                                ->weight(weight: FontWeight::SemiBold),
                                        ])
                                            ->verticallyAlignCenter()
                                            ->from('lg'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function makeInfolist(): Infolist
    {
        return parent::makeInfolist()
            ->record($this->getElection());
    }

    protected function getCandidateVotes(string $key, ?int $boothId = null): string
    {
        return $this->getElection()->result?->meta->toCollection()
            ->when(
                filled($boothId),
                fn (Collection $collection) => $collection->where('key', "$key:booth:$boothId"),
                fn (Collection $collection) => $collection->where('key', "$key"),
            )
            ->first()
            ?->value ?? '0';
    }

    protected function generateEmptyStatePlaceholder(string $heading, ?string $description = null, ?string $icon = null, array $actions = []): HtmlString
    {
        return new HtmlString(
            html: Blade::render(
                string: <<<'HTML'
<x-filament::section>
    <x-filament.state
        :heading="$heading"
        :description="$description"
        :icon="$icon"
        :actions="$actions"
    />
</x-filament::section>
HTML,
                data: [
                    'heading' => $heading,
                    'description' => $description,
                    'icon' => $icon,
                    'actions' => $actions,
                ]
            ),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            SelectAction::make(name: 'sortBy')
                ->options(options: ElectionResultSortBy::getOptions())
                ->placeholder(placeholder: 'Sort by')
                ->label(label: fn () => $this->sortBy?->getLabel()),

            SelectAction::make(name: 'boothId')
                ->options(options: $this->getElection()->boothTokens()->pluck('name', 'id'))
                ->placeholder(placeholder: 'All Booths')
                ->label(label: fn () => $this->getElection()->boothTokens()->find(id: $this->boothId)?->name)
                ->visible(condition: $this->getElection()->preference->booth_voting),

            $this->getDownloadAction(),
        ];
    }

    protected function getDownloadAction(): Action
    {
        return Action::make(name: 'download')
            ->action(action: function (self $livewire) {
                $election = $livewire->getElection();
                $pdf = app(GenerateResultPdf::class)->execute($election);

                return response()->streamDownload(function () use ($pdf) {
                    echo base64_decode($pdf->base64());
                }, 'result-' . $election->getRouteKey() . '.pdf');
            })
            ->icon(icon: 'heroicon-s-arrow-down-tray');
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::canAccessPage(election: $parameters['record']);
    }
}
