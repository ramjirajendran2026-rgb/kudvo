<?php

namespace App\Livewire\Survey;

use App\Actions\Survey\SubmitSurveyResponse;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Filament\Forms\Components\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

/**
 * @property Form $form
 */
class EntryForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Survey $survey;

    public ?array $data = null;

    public bool $isSubmitted = false;

    public bool $isDisabled = false;

    public bool $isPreview;

    public function mount(): void
    {
        $this->isPreview ??= request()->routeIs('survey.preview') || ! $this->survey->is_published || ! $this->survey->is_active;

        $this->authorizePageAccess();

        $this->form->fill($this->data);
    }

    protected function authorizePageAccess(): void
    {
        if ($this->isPreview) {
            return;
        }

        $this->authorize('create-response', $this->survey);
    }

    public function render()
    {
        return view('livewire.survey.entry-form')
            ->layout('components.layouts.base')
            ->layoutData([
                'seoData' => $this->survey,
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled($this->isDisabled)
            ->statePath('data')
            ->schema([
                ...$this->survey->questions->map(
                    fn (SurveyQuestion $question) => $question->type->getFormComponent($question)
                )->filter()->toArray(),

                Actions::make([
                    Actions\Action::make('submit')
                        ->action('submit'),

                    Actions\Action::make('clear')
                        ->action(fn () => $this->form->fill())
                        ->color('gray')
                        ->extraAttributes([
                            'wire:dirty' => true,
                            'wire:target' => 'data',
                        ]),
                ])->hidden(fn () => $this->isDisabled),
            ]);
    }

    public function submit(): void
    {
        abort_if($this->isDisabled, 403);

        $this->authorizePageAccess();

        $data = $this->form->getState();

        if ($this->isPreview) {
            $this->isSubmitted = true;

            return;
        }

        app(SubmitSurveyResponse::class)->execute($this->survey, $data);

        $this->isSubmitted = true;
    }
}
