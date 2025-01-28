<?php

namespace App\Livewire\Survey;

use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Layout;
use Livewire\Component;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Models\Survey;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * @property Form $form
 */
#[Layout('components.layouts.base')]
class Entry extends Component implements HasForms
{
    use InteractsWithForms;

    public Survey $survey;

    public ?array $data = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.survey.entry', ['survey' => $this->survey])
            ->layoutData([
                'seoData' => new SEOData(
                    title: $this->survey->name,
                    description: str($this->survey->settings['description'] ?? '')->stripTags(),
                ),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$this->survey->questions->map(fn (Question $question) => match ($question->type) {
                    'text' => $this->textQuestion($question),
                    'radio' => $this->radioQuestion($question),
                    default => null,
                })->filter()->toArray(),

                Turnstile::make('captcha'),

                Actions::make([
                    Actions\Action::make('submit')
                        ->submit('submit'),
                ])->alignCenter(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        (new \MattDaneshvar\Survey\Models\Entry)->for($this->survey)->fromArray($data)->push();

        $this->form->fill();

        $redirectUrl = route('products.election.home');

        $this->js(
            <<<JS
Swal.fire({
    text: 'Thank you for your response',
    type: 'success',
    icon: 'success'
}).then((result) => {
    if (result.isConfirmed || result.isDismissed) {
        window.location.href = '$redirectUrl';
    }
})
JS
        );
    }

    protected function textQuestion(Question $question)
    {
        return TextInput::make($question->key)
            ->label(new HtmlString($question->content));
    }

    protected function radioQuestion(Question $question)
    {
        return Radio::make($question->key)
            ->columns(3)
            ->gridDirection('row')
            ->label(new HtmlString($question->content))
            ->options(Arr::mapWithKeys($question->options, fn ($option, $key) => [
                $option => $option,
            ]));
    }
}
