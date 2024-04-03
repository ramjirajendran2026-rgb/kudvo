<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Result extends ElectionPage
{
    protected static string $view = 'filament.user.resources.election-resource.pages.result';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chart-pie';

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election) &&
            static::can(action: 'viewResult', election: $election);
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
                            ->collapsible()
                            ->compact()
                            ->description(description: fn (Position $state): ?string => $state->quota.Str::plural(value: ' Post', count: $state->quota))
                            ->schema(components: [
                                RepeatableEntry::make(name: 'rankedCandidates')
                                    ->extraAttributes(attributes: ['class' => 'candidate-repeatable-entry [&_.fi-fo-component-ctn]:gap-2'])
                                    ->hiddenLabel()
                                    ->placeholder(placeholder: $this->generateEmptyStatePlaceholder(
                                        heading: 'No candidates',
                                        description: 'Create new candidate',
                                        icon: 'heroicon-o-x-mark',
                                    ))
                                    ->schema(components: [
                                        Split::make(schema: [
                                            SpatieMediaLibraryImageEntry::make(name: 'photo')
                                                ->circular()
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => filament()->getUserAvatarUrl($record))
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 80)
                                                ->visible(condition: $this->getElection()->preference?->candidate_photo),

                                            Group::make()
                                                ->schema(components: [
                                                    TextEntry::make(name: 'display_name')
                                                        ->hiddenLabel()
                                                        ->size(size: TextEntry\TextEntrySize::Large)
                                                        ->weight(weight: FontWeight::Medium),

                                                    TextEntry::make(name: 'membership_number')
                                                        ->color(color: 'gray')
                                                        ->getStateUsing(
                                                            callback: fn (Candidate $record): ?string => collect(value: [
                                                                $record->membership_number,
                                                                $record->email,
                                                                $record->phone
                                                            ])->filter(callback: fn (?string $item): bool => filled(value: $item))->implode(value: ' • ')
                                                        )
                                                        ->hiddenLabel()
                                                        ->visible(condition: fn (?string $state): bool => filled($state)),
                                                ]),

                                            SpatieMediaLibraryImageEntry::make(name: 'symbol')
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_SYMBOL)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => $record->symbol_url)
                                                ->extraImgAttributes(attributes: ['class' => 'rounded-xl'])
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 80)
                                                ->visible(condition: $this->getElection()->preference?->candidate_symbol),
                                        ]),

                                        TextEntry::make(name: 'votes')
                                            ->alignCenter()
                                            ->color(color: 'primary')
                                            ->extraAttributes(attributes: ['class' => 'bg-gray-50 dark:bg-white/5 rounded-lg py-2'])
                                            ->formatStateUsing(callback: fn (int $state): string => Str::plural(value: "$state vote", count: $state))
                                            ->getStateUsing(callback: fn (Candidate $record) => $this->getElection()->result?->meta->toCollection()->firstWhere('key', $record->uuid)?->value ?? 0)
                                            ->grow(condition: false)
                                            ->hiddenLabel()
                                            ->size(size: TextEntry\TextEntrySize::Large)
                                            ->weight(weight: FontWeight::Medium),
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
            $this->getDownloadAction(),
        ];
    }

    protected function getDownloadAction(): Action
    {
        return Action::make(name: 'download')
            ->action(action: function (self $livewire) {
                $election = $livewire->getElection();

                $pdf = Pdf::loadView(
                    'pdf.election.result',
                    [
                        'election' => $election,
                    ],
                    [],
                    'UTF-8'
                );

                return response()
                    ->streamDownload(
                        callback: function () use ($pdf) {
                            echo $pdf->output();
                        },
                        name: "result-{$this->getElection()->code}.pdf",
                    );
            })
            ->icon(icon: 'heroicon-s-arrow-down-tray');
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::canAccessPage(election: $parameters['record']);
    }
}
