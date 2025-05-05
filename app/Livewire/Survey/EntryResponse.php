<?php

namespace App\Livewire\Survey;

use App\Actions\Survey\GenerateReferenceNumber;
use App\Enums\SurveyQuestionType;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Livewire\Component;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * @property Form $form
 */
class EntryResponse extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public Survey $survey;

    public SurveyResponse $surveyResponse;

    public ?array $data = null;

    public function mount(): void
    {
        $this->authorizePageAccess();

        $this->data = SurveyAnswer::whereBelongsTo($this->surveyResponse, 'response')
            ->pluck('content', 'question_id')
            ->mapWithKeys(fn ($content, $questionId) => [SurveyQuestion::KEY_PREFIX . $questionId => $content])
            ->toArray() ?? [];

        $this->form->fill($this->data);
    }

    protected function authorizePageAccess(): void
    {
        // $this->authorize('view-response', $this->survey);
    }

    public function render()
    {
        return view('livewire.survey.entry-response')
            ->layout('components.layouts.base')
            ->layoutData([
                'seoData' => new SEOData(
                    title: app(GenerateReferenceNumber::class)->execute($this->surveyResponse, $this->survey),
                    enableTitleSuffix: false,
                ),
            ]);
    }

    public function responseInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->data)
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        Group::make([
                            ...$this->survey->questions
                                ->filter(fn (SurveyQuestion $question) => $question->type !== SurveyQuestionType::Photo)
                                ->map(
                                    fn (SurveyQuestion $question) => $question->type->getInfolistComponent($question)
                                )->filter()->toArray(),
                        ])->columnSpan(2)->inlineLabel(),

                        Group::make([
                            ...$this->survey->questions
                                ->filter(fn (SurveyQuestion $question) => $question->type === SurveyQuestionType::Photo)
                                ->map(
                                    fn (SurveyQuestion $question) => $question->type->getInfolistComponent($question)
                                )->filter()->toArray(),
                        ]),
                    ]),
            ]);
    }
}
