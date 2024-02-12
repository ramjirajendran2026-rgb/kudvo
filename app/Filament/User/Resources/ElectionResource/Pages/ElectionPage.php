<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Facades\Kudvo;
use App\Filament\Contracts\HasElection;
use App\Filament\Contracts\HasElectorGroups;
use App\Filament\User\Resources\ElectionResource;
use App\Forms\Components\VotePicker;
use App\Models\Election;
use App\Models\Position;
use Cookie;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property Form $form
 */
abstract class ElectionPage extends Page implements HasElectorGroups, HasElection
{
    protected static string $resource = ElectionResource::class;

    #[Locked]
    public Election $election;

    #[Locked]
    public array $electorGroups = [];

    public function mount(int|string $record): void
    {
        $this->election = $this->resolveElection(key: $record);

        $this->authorizeAccess();

        $this->electorGroups = $this->getElection()->getElectorGroups();
    }

    public function mountCanAuthorizeAccess(): void
    {
    }

    protected function resolveElection(int | string $key): Election
    {
        /** @var Election $nomination */
        $nomination = app(ElectionResource::getModel())
            ->resolveRouteBindingQuery(ElectionResource::getEloquentQuery(), $key, ElectionResource::getRecordRouteKeyName())
            ->withCount(relations: ['positions', 'electors'])
            ->first();

        abort_if(boolean: blank(value: $nomination), code: Response::HTTP_NOT_FOUND);

        return $nomination;
    }

    public function getElection(): Election
    {
        return $this->election;
    }

    public function getElectorGroups(): array
    {
        return $this->electorGroups;
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel().' - '.$this->getRecordTitle();
    }

    public function getRecordTitle(): string | Htmlable
    {
        if (! ElectionResource::hasRecordTitle()) {
            return ElectionResource::getTitleCaseModelLabel();
        }

        return ElectionResource::getRecordTitle($this->getElection());
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getRecordTitle();
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! $this->getElection()->isTimingConfigured()) {
            return null;
        }

        return new HtmlString(
            html: <<<HTML
<b>{$this->getElection()->starts_at_local->format(format: 'M d, Y h:i A (T)')}</b> to
<b>{$this->getElection()->ends_at_local->format(format: 'M d, Y h:i A (T)')}</b>
HTML
        );
    }

    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getElection(),
        ];
    }

    public function getSubNavigation(): array
    {
        return ElectionResource::getRecordSubNavigation($this);
    }

    public function generateNavigationItems(array $components): array
    {
        $election = $this->getElection();

        if (
            in_array(needle: MonitorTokens::class, haystack: $components) &&
            ! $election->is_published
        ) {
            unset($components[array_search(needle: MonitorTokens::class, haystack: $components)]);
        }

        return parent::generateNavigationItems($components);
    }

    public function getWidgetData(): array
    {
        return [
            'record' => $this->getElection(),
            'nomination' => $this->getElection(),
        ];
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return $this->getElection();
    }

    public function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        if (! static::canAccessPage(election: $this->election)) {
            Notification::make()
                ->title(title: 'Not allowed')
                ->body(body: 'Complete previous steps before accessing this page')
                ->warning()
                ->send();

            $this->redirect(Dashboard::getUrl(parameters: [$this->election]));
        }
    }

    protected function configureAction(Action $action): void
    {
        $action
            ->record($this->getElection())
            ->recordTitle($this->getRecordTitle());
    }

    protected function getPreviewBallotAction()
    {
        return Action::make(name: 'previewBallot')
            ->authorize(abilities: 'preview')
            ->action(action: function (Action $action, array $data, Form $form): void {
                $preview = $data['preview'];

                if ($preview) {
                    Notification::make()
                        ->title(title: 'Preview completed')
                        ->success()
                        ->send();

                    return;
                }

                $data['preview'] = true;
                $data['votes'] = Arr::mapWithKeys($data['votes'], fn ($item, $key) => [$key => Arr::map($item, fn ($subItem) => $subItem['key'])]);

                $form->fill(state: $data);

                $action->formData(data: $data);
                $action->halt();
            })
            ->color(color: 'success')
            ->form(
                form: fn (HasElection $livewire): array => [
                    Hidden::make(name: 'preview')
                        ->default(state: false),

                    Group::make()
                        ->statePath(path: 'votes')
                        ->schema(components: $livewire->getElection()->positions
                            ->map(
                                callback: fn (Position $position) => VotePicker::makeFor(position: $position)
                                    ->disabled(condition: fn (Get $get): bool => $get(path: '../preview'))
                                    ->photo(condition: $this->getElection()->preference->candidate_photo)
                                    ->preview(condition: fn (Get $get): bool => $get(path: '../preview'))
                                    ->symbol(condition: $this->getElection()->preference->candidate_symbol),
                            )
                            ->toArray()
                        )
                ]
            )
            ->icon(icon: 'heroicon-m-eye')
            ->label(label: 'Preview')
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalDescription(description: $this->getSubheading())
            ->modalHeading(heading: $this->getHeading())
            ->modalCancelAction(action: false)
            ->modalSubmitActionLabel(label: fn (array $data): string => ($data['preview'] ?? false) ? 'Confirm' : 'Continue')
            ->slideOver();
    }

    public static function can(string $action, Election $election): bool
    {
        return ElectionResource::can(action: $action, record: $election);
    }

    public static function cannot(string $action, Election $election): bool
    {
        return ! static::can(action: $action, election: $election);
    }

    public static function canAccessPage(Election $election): bool
    {
        return ElectionResource::canView(record: $election);
    }
}
