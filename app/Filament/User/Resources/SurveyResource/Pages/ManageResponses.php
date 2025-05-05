<?php

namespace App\Filament\User\Resources\SurveyResource\Pages;

use App\Actions\Survey\GenerateReferenceNumber;
use App\Enums\SurveyQuestionType;
use App\Enums\SurveyResponsesPageTabs;
use App\Exports\SurveyResponsesExport;
use App\Filament\User\Resources\SurveyResource;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ManageResponses extends Page implements HasForms
{
    use InteractsWithRecord;

    protected static string $resource = SurveyResource::class;

    protected static string $view = 'filament.user.resources.survey-resource.pages.manage-responses';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationLabel = 'Responses';

    public ?string $previousUrl = null;

    public SurveyResponsesPageTabs $activeTab = SurveyResponsesPageTabs::Summary;

    public ?int $activeResponseId = null;

    public static function canAccess(array $parameters = []): bool
    {
        return SurveyResource::can('viewResponses', $parameters['record'] ?? null);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getRefreshAction(),

            SurveyResource::getSettingsAction(),

            ActionGroup::make([
                Action::make('download')
                    ->action(function (self $livewire) {
                        return Excel::download(new SurveyResponsesExport($livewire->getSurvey()), Str::slug($livewire->getSurvey()->title) . '-responses.xlsx');
                    })
                    ->icon('heroicon-s-arrow-down-tray'),

                Action::make('openSeparately')
                    ->url(URL::signedRoute('survey.responses', [
                        'survey' => $this->getSurvey()->id,
                    ]), true)
                    ->icon('heroicon-s-arrow-up-tray'),
            ])->dropdownPlacement('bottom-end'),

            SurveyResource::getCopyLinkAction()
                ->outlined(),

            SurveyResource::getShareAction()
                ->outlined(),

            SurveyResource::getPreviewAction(),

            SurveyResource::getPublishAction(),
        ];
    }

    protected function getRefreshAction(): Action
    {
        return Action::make('refresh')
            ->action(
                fn () => Notification::make()
                    ->title('Refreshed')
                    ->success()
                    ->send()
            )
            ->icon('heroicon-o-arrow-path')
            ->iconButton();
    }

    protected function getSurvey(): Survey
    {
        /** @var Survey $survey */
        $survey = $this->getRecord();

        return $survey;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Survey #' . $this->getRecord()->getKey();
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->activeResponseId = $this->getSurvey()
            ->responses()->latest('sort')->first()?->getKey();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(SurveyResource::can('viewResponses', $this->getRecord()), 403);
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return SurveyResponsesPageTabs::cases();
    }

    public function getSummaryItems(): Collection
    {
        return $this->getSurvey()
            ->questions()
            ->with('answers:question_id,content')
            ->get()
            ->map(function ($question) {
                $question->answers = match ($question->type) {
                    SurveyQuestionType::Checkboxes => $question->answers->pluck('content')->flatten()->countBy()->sortDesc(),
                    default => $question->answers->countBy('content_formatted')->sortDesc()
                };

                return $question;
            })
            ->filter();
    }

    public function getResponseNumbers(): array
    {
        $generator = app(GenerateReferenceNumber::class);

        return $this->getSurvey()
            ->responses
            ->mapWithKeys(fn (SurveyResponse $surveyResponse) => [$surveyResponse->id => $generator->execute($surveyResponse, $this->getSurvey())])
            ->toArray();
    }

    public function activeResponseData(): array
    {
        return SurveyAnswer::where('response_id', $this->activeResponseId)
            ->get()
            ->mapWithKeys(fn ($answer) => [SurveyQuestion::KEY_PREFIX . $answer->question_id => $answer->content])
            ->toArray() ?? [];
    }

    public function deleteActiveResponse(): Action
    {
        return Action::make('deleteActiveResponse')
            ->requiresConfirmation()
            ->icon('heroicon-s-trash')
            ->iconButton()
            ->label('Delete')
            ->color('danger')
            ->successNotificationTitle('Deleted')
            ->action(function (self $livewire, Action $action) {
                $livewire->getActiveResponse()->delete();
                $livewire->activeResponseId = $this->getSurvey()
                    ->responses()->latest('sort')->first()?->getKey();

                $action->success();
            });
    }

    public function getActiveResponse(): ?SurveyResponse
    {
        return $this->getSurvey()->responses()->find($this->activeResponseId);
    }
}
