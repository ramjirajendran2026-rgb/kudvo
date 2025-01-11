<?php

namespace App\Forms\Components;

use App\Enums\ResolutionChoice;
use App\Models\Resolution;
use Filament\Forms\Components\ToggleButtons;

class ResolutionChoicePicker extends ToggleButtons
{
    protected string $view = 'forms.components.resolution-choice-picker';

    protected Resolution $resolution;

    public static function makeFor(Resolution $resolution)
    {
        $static = app(static::class, ['name' => $resolution->getKey()]);
        $static->resolution($resolution);
        $static->configure();

        return $static;
    }

    public function resolution(Resolution $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getChoices(): array
    {
        $resolution = $this->getResolution();

        return [
            ResolutionChoice::For->value => $resolution->for_label ?: ResolutionChoice::For->getLabel(),
            ResolutionChoice::Against->value => $resolution->against_label ?: ResolutionChoice::Against->getLabel(),
            ...$this->hasAbstain() ? [ResolutionChoice::Abstain->value => $resolution->abstain_label ?: ResolutionChoice::Abstain->getLabel()] : [],
        ];
    }

    public function getContent()
    {
        return $this->getResolution()->description;
    }

    public function getHeading()
    {
        return $this->getResolution()->name;
    }

    public function getResolution(): Resolution
    {
        return $this->resolution;
    }

    public function hasAbstain()
    {
        return $this->getResolution()->allow_abstain_votes;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $resolution = $this->getResolution();

        $this->colors([
            ResolutionChoice::For->value => ResolutionChoice::For->getColor(),
            ResolutionChoice::Against->value => ResolutionChoice::Against->getColor(),
            ...$this->hasAbstain() ? [ResolutionChoice::Abstain->value => ResolutionChoice::Abstain->getColor()] : [],
        ]);

        $this->extraAttributes([
            'class' => 'resolution-choice-picker',
        ]);

        $this->hiddenLabel();

        $this->icons([
            ResolutionChoice::For->value => ResolutionChoice::For->getIcon(),
            ResolutionChoice::Against->value => ResolutionChoice::Against->getIcon(),
            ...$this->hasAbstain() ? [ResolutionChoice::Abstain->value => ResolutionChoice::Abstain->getIcon()] : [],
        ]);

        $this->label($resolution->name);

        $this->options([
            ResolutionChoice::For->value => $resolution->for_label ?: ResolutionChoice::For->getLabel(),
            ResolutionChoice::Against->value => $resolution->against_label ?: ResolutionChoice::Against->getLabel(),
            ...$this->hasAbstain() ? [ResolutionChoice::Abstain->value => $resolution->abstain_label ?: ResolutionChoice::Abstain->getLabel()] : [],
        ]);

        $this->required();

        $this->validationMessages(['required' => 'Please choose your response to this resolution.']);
    }
}
