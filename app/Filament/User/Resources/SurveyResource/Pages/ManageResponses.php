<?php

namespace App\Filament\User\Resources\SurveyResource\Pages;

use App\Enums\SurveyQuestionType;
use App\Enums\SurveyResponsesPageTabs;
use App\Filament\User\Resources\SurveyResource;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Filament\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;

class ManageResponses extends Page implements HasForms
{
    use InteractsWithRecord;

    protected static string $resource = SurveyResource::class;

    protected static string $view = 'filament.user.resources.survey-resource.pages.manage-responses';

    public ?string $previousUrl = null;

    public SurveyResponsesPageTabs $activeTab = SurveyResponsesPageTabs::Summary;

    public ?int $activeResponseId = null;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->activeResponseId = $this->getSurvey()
            ->responses()->latest('sort')->first()?->getKey();

        $this->previousUrl = url()->previous();
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::ScreenMedium;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string | Htmlable
    {
        return 'Survey #' . $this->getRecord()->getKey();
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
        return $this->getSurvey()
            ->responses()
            ->pluck('sort', 'id')
            ->toArray();
    }

    public function getActiveResponse(): ?SurveyResponse
    {
        return $this->getSurvey()->responses()->find($this->activeResponseId);
    }

    public function activeResponseData(): array
    {
        return SurveyAnswer::where('response_id', $this->activeResponseId)
            ->get()
            ->mapWithKeys(fn ($answer) => [SurveyQuestion::KEY_PREFIX . $answer->question_id => $answer->content])
            ->toArray() ?? [];
    }

    protected function getSurvey(): Survey
    {
        /** @var Survey $survey */
        $survey = $this->getRecord();

        return $survey;
    }

    protected function authorizeAccess(): void
    {
        abort_unless(SurveyResource::can('viewResponses', $this->getRecord()), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->action(
                    fn () => Notification::make()
                        ->title('Refreshed')
                        ->success()
                        ->send()
                )
                ->icon('heroicon-o-arrow-path')
                ->iconButton(),

            SurveyResource::getSettingsAction(),

            SurveyResource::getCopyLinkAction()
                ->outlined(),

            SurveyResource::getShareAction()
                ->outlined(),

            SurveyResource::getPreviewAction(),

            SurveyResource::getPublishAction(),

            SurveyResource::getEditPageAction(),
        ];
    }
}
