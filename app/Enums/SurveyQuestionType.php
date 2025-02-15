<?php

namespace App\Enums;

use App\Models\SurveyQuestion;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

enum SurveyQuestionType: string implements HasLabel
{
    case ShortAnswer = 'short_answer';
    case Paragraph = 'paragraph';
    case MultipleChoice = 'multiple_choice';
    case Checkboxes = 'checkboxes';
    case Date = 'date';
    case Time = 'time';
    case DateTime = 'datetime';
    case Email = 'email';
    case Phone = 'phone';
    case Number = 'number';
    case Url = 'url';

    public static function getOptions(): array
    {
        return Arr::mapWithKeys(self::cases(), fn (self $case): array => [$case->value => $case->getLabel()]);
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ShortAnswer => 'Short Answer',
            self::Paragraph => 'Paragraph',
            self::MultipleChoice => 'Multiple Choice',
            self::Checkboxes => 'Checkboxes',
            self::Date => 'Date',
            self::Time => 'Time',
            self::DateTime => 'Date & Time',
            self::Email => 'Email',
            self::Phone => 'Phone',
            self::Number => 'Number',
            self::Url => 'URL',
        };
    }

    public function canHaveOption(): bool
    {
        return match ($this) {
            self::MultipleChoice, self::Checkboxes => true,
            default => false,
        };
    }

    public function canHaveOtherOption(): bool
    {
        return match ($this) {
            self::MultipleChoice, self::Checkboxes => true,
            default => false,
        };
    }

    public function getFormComponent(SurveyQuestion $question): ?Section
    {
        $components = match ($this) {
            self::ShortAnswer => $this->getShortAnswerComponent($question),
            self::Paragraph => $this->getParagraphComponent($question),
            self::MultipleChoice => $this->getMultipleChoiceComponent($question),
            self::Checkboxes => $this->getCheckboxesComponent($question),
            self::Date => $this->getDateComponent($question),
            self::Time => $this->getTimeComponent($question),
            self::DateTime => $this->getDateTimeComponent($question),
            self::Email => $this->getEmailComponent($question),
            self::Phone => $this->getPhoneComponent($question),
            self::Number => $this->getNumberComponent($question),
            self::Url => $this->getUrlComponent($question),
        };

        if (blank($components)) {
            return null;
        }

        return Section::make()
            ->compact()
            ->schema(Arr::wrap($components));
    }

    protected function makeTextInputComponent(SurveyQuestion $question): TextInput
    {
        return TextInput::make($question->key)
            ->label($question->text)
            ->placeholder('Your answer')
            ->required($question->is_required);
    }

    protected function getShortAnswerComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question);
    }

    protected function getParagraphComponent(SurveyQuestion $question): Textarea
    {
        return Textarea::make($question->key)
            ->label($question->text)
            ->placeholder('Your answer')
            ->required($question->is_required);
    }

    protected function getMultipleChoiceComponent(SurveyQuestion $question): Radio
    {
        return Radio::make($question->key)
            ->label($question->text)
            ->options(Arr::mapWithKeys($question->options, fn ($option) => [$option => $option]))
            ->required($question->is_required);
    }

    protected function getCheckboxesComponent(SurveyQuestion $question): CheckboxList
    {
        return CheckboxList::make($question->key)
            ->label($question->text)
            ->options(Arr::mapWithKeys($question->options, fn ($option) => [$option => $option]))
            ->required($question->is_required);
    }

    protected function getDateComponent(SurveyQuestion $question): DatePicker
    {
        return DatePicker::make($question->key)
            ->label($question->text)
            ->required($question->is_required);
    }

    protected function getTimeComponent(SurveyQuestion $question): TimePicker
    {
        return TimePicker::make($question->key)
            ->label($question->text)
            ->required($question->is_required);
    }

    protected function getDateTimeComponent(SurveyQuestion $question): DateTimePicker
    {
        return DateTimePicker::make($question->key)
            ->label($question->text)
            ->required($question->is_required);
    }

    protected function getEmailComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question)
            ->email();
    }

    protected function getPhoneComponent(SurveyQuestion $question): PhoneInput
    {
        return PhoneInput::make($question->key)
            ->label($question->text)
            ->required($question->is_required)
            ->validateFor();
    }

    protected function getNumberComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question)
            ->numeric();
    }

    protected function getUrlComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question)
            ->url();
    }
}
