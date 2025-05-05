<?php

namespace App\Livewire\Survey;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

/**
 * @property Form $form
 */
class EntryResponse extends Component implements HasForms
{
    use InteractsWithForms;

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
        //        $this->authorize('view-response', $this->survey);
    }

    public function render()
    {
        return view('livewire.survey.entry-response')
            ->layout('components.layouts.base')
            ->layoutData([
                'seoData' => $this->survey,
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                ...$this->survey->questions->map(
                    fn (SurveyQuestion $question) => $question->type->getFormComponent($question)
                )->filter()->toArray(),
            ]);
    }
}
