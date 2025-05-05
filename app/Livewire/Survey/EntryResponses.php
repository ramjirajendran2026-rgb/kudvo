<?php

namespace App\Livewire\Survey;

use App\Enums\SurveyQuestionType;
use App\Models\Survey;
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
class EntryResponses extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public Survey $survey;

    public ?array $data = null;

    public function mount(): void
    {
        $this->authorizePageAccess();

        $this->data = $this->survey->responses->load('answers')
            ->mapWithKeys(fn (SurveyResponse $response) => [
                'resp-' . $response->id => $response->answers->pluck('content', 'question_id')->mapWithKeys(fn ($content, $questionId) => [SurveyQuestion::KEY_PREFIX . $questionId => $content]),
            ])
            ->toArray() ?? [];

        $this->form->fill($this->data);
    }

    protected function authorizePageAccess(): void
    {
        // $this->authorize('view-response', $this->survey);
    }

    public function render()
    {
        return view('livewire.survey.entry-responses')
            ->layout('components.layouts.base')
            ->layoutData([
                'seoData' => new SEOData(
                    title: $this->survey->title . ' - Responses',
                    enableTitleSuffix: false,
                ),
            ]);
    }

    public function responseInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->data)
            ->schema([
                ...$this->survey->responses
                    ->map(
                        fn (SurveyResponse $response) => Group::make([
                            Section::make($this->survey->title)
                                ->extraAttributes(['class' => '[&_.fi-section-header-heading]:text-center !shadow-none'])
                                ->columns(['sm' => 3])
                                ->statePath('resp-' . $response->id)
                                ->schema([
                                    Group::make([
                                        ...$this->survey->questions
                                            ->filter(fn (SurveyQuestion $question) => $question->type !== SurveyQuestionType::Photo)
                                            ->map(
                                                fn (SurveyQuestion $question) => $question->type->getInfolistComponent($question)
                                            )->filter()->toArray(),
                                    ])->columnSpan(['sm' => 2])->inlineLabel(),

                                    Group::make([
                                        ...$this->survey->questions
                                            ->filter(fn (SurveyQuestion $question) => $question->type === SurveyQuestionType::Photo)
                                            ->map(
                                                fn (SurveyQuestion $question) => $question->type->getInfolistComponent($question)
                                            )->filter()->toArray(),
                                    ]),
                                ]),
                        ])->extraAttributes(['class' => 'break-after-page pt-12'])
                    ),

            ]);
    }
}
