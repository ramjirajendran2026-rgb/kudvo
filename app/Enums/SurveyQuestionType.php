<?php

namespace App\Enums;

use App\Forms\Components\OtpInput;
use App\Models\OneTimePassword;
use App\Models\SurveyQuestion;
use App\Notifications\OneTimePasswordNotification;
use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
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
    case MonthYear = 'month_year';
    case Email = 'email';
    case Phone = 'phone';
    case VerifiedPhone = 'verified_phone';
    case Number = 'number';
    case Url = 'url';
    case Photo = 'photo';

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->when(
                ! auth()->user()?->hasAdminRole(),
                fn ($collection) => $collection->filter(fn (self $case) => $case !== self::VerifiedPhone)
            )
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->getLabel()])
            ->toArray();
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
            self::MonthYear => 'Month & Year',
            self::Email => 'Email',
            self::Phone => 'Phone number',
            self::VerifiedPhone => 'Verified phone number',
            self::Number => 'Number',
            self::Url => 'URL',
            self::Photo => 'Photo',
        };
    }

    public function canBeUnique(): bool
    {
        return match ($this) {
            self::ShortAnswer,
            self::Email,
            self::Phone,
            self::VerifiedPhone => true,
            default => false,
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

    public function getAnswerOutput(SurveyQuestion $question, ?string $answer): ?string
    {
        return match ($this) {
            self::Photo => new HtmlString(sprintf(
                '<a href="%s" target="_blank" >%s</a>',
                Storage::disk(config('filament.default_filesystem_disk'))->url($answer),
                Str::afterLast($answer, '/'),
            )),
            default => new HtmlString("<span>$answer</span>"),
        };
    }

    public function getFormComponent(SurveyQuestion $question, bool $isPreview = false): ?Section
    {
        $components = match ($this) {
            self::ShortAnswer => $this->getShortAnswerComponent($question),
            self::Paragraph => $this->getParagraphComponent($question),
            self::MultipleChoice => $this->getMultipleChoiceComponent($question),
            self::Checkboxes => $this->getCheckboxesComponent($question),
            self::Date => $this->getDateComponent($question),
            self::Time => $this->getTimeComponent($question),
            self::DateTime => $this->getDateTimeComponent($question),
            self::MonthYear => $this->getMonthYearPickerComponent($question),
            self::Email => $this->getEmailComponent($question),
            self::Phone => $this->getPhoneComponent($question),
            self::VerifiedPhone => $this->getVerifiedPhoneComponent($question),
            self::Number => $this->getNumberComponent($question),
            self::Url => $this->getUrlComponent($question),
            self::Photo => $this->getPhotoComponent($question, $isPreview),
        };

        if (blank($components)) {
            return null;
        }

        return Section::make()
            ->compact()
            ->schema(Arr::wrap($components));
    }

    protected function getShortAnswerComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question);
    }

    protected function makeTextInputComponent(SurveyQuestion $question): TextInput
    {
        return TextInput::make($question->key)
            ->label($question->text)
            ->placeholder('Your answer')
            ->required($question->is_required);
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
            ->required($question->is_required)
            ->rule(
                Rule::unique('survey_answers', 'content')
                    ->where('question_id', $question->getKey()),
                $question->settings['unique'] ?? false,
            );
    }

    protected function getCheckboxesComponent(SurveyQuestion $question): CheckboxList
    {
        return CheckboxList::make($question->key)
            ->label($question->text)
            ->options(Arr::mapWithKeys($question->options, fn ($option) => [$option => $option]))
            ->required($question->is_required)
            ->rule(
                Rule::unique('survey_answers', 'content')
                    ->where('question_id', $question->getKey()),
                $question->settings['unique'] ?? false,
            );
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

    protected function getMonthYearPickerComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question)
            ->rules([
                'date',
                'date_format:Y-m',
            ])
            ->type('month')
            ->when(
                $question->settings['month_year']['max'] ?? null,
                fn (TextInput $component, string $value) => $component
                    ->extraInputAttributes(['max' => $value], true)
                    ->rule('before_or_equal:' . $value)
            )
            ->when(
                $question->settings['month_year']['min'] ?? null,
                fn (TextInput $component, string $value) => $component
                    ->extraInputAttributes(['min' => $value], true)
                    ->rule('after_or_equal:' . $value)
            );
    }

    protected function getEmailComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question)
            ->email()
            ->rule(
                Rule::unique('survey_answers', 'content')
                    ->where('question_id', $question->getKey()),
                $question->settings['unique'] ?? false,
            );
    }

    protected function getPhoneComponent(SurveyQuestion $question): PhoneInput
    {
        return PhoneInput::make($question->key)
            ->defaultCountry(request()->ipinfo?->country ?? '')
            ->label($question->text)
            ->required($question->is_required)
            ->rule(
                Rule::unique('survey_answers', 'content')
                    ->where('question_id', $question->getKey()),
                $question->settings['unique'] ?? false,
            )
            ->validateFor();
    }

    protected function getVerifiedPhoneComponent(SurveyQuestion $question): array
    {
        return [
            Hidden::make($question->key . '_otp_id')
                ->dehydrated(false)
                ->default(null),

            PhoneInput::make($question->key)
                ->disableIpLookUp()
                ->defaultCountry('IN')
                ->initialCountry('IN')
                ->hidden(fn (Get $get, Field $component) => filled($get($question->key . '_otp_id')) || $component->getContainer()->isDisabled())
                ->hintActions([
                    Action::make('get_otp')
                        ->action(function (Component $livewire, PhoneInput $component, Set $set) use ($question) {
                            $livewire->validateOnly($component->getStatePath());

                            $otp = OneTimePassword::create([
                                'phone' => $component->getState(),
                                'code' => rand(1000, 9999),
                            ]);

                            Notification::route('sms', $component->getState())
                                ->notifyNow(new OneTimePasswordNotification($otp));

                            $set($question->key . '_otp_id', $otp->id);
                        })
                        ->label('Get OTP')
                        ->visible(fn (Get $get) => blank($get($question->key . '_otp_id'))),
                ])
                ->label($question->text)
                ->required($question->is_required)
                ->rule(
                    Rule::unique('survey_answers', 'content')
                        ->where('question_id', $question->getKey()),
                    $question->settings['unique'] ?? false,
                )
                ->validateFor(),

            TextInput::make($question->key)
                ->readOnly()
                ->hintActions([
                    Action::make('edit')
                        ->action(fn (Set $set) => $set($question->key . '_otp_id', null))
                        ->hidden(fn (Field $component) => $component->getContainer()->isDisabled())
                        ->icon('heroicon-s-pencil-square'),
                ])
                ->label($question->text)
                ->required($question->is_required)
                ->visible(fn (Get $get, Field $component) => filled($get($question->key . '_otp_id')) || $component->getContainer()->isDisabled()),

            OtpInput::make($question->key . '_otp')
                ->dehydrated(false)
                ->disabled(fn (Get $get) => blank($get($question->key . '_otp_id')))
                ->hidden(fn (Get $get, Field $component) => OneTimePassword::find($get($question->key . '_otp_id'))?->isVerified() || $component->getContainer()->isDisabled())
                ->hiddenLabel(false)
                ->label('OTP')
                ->length(4)
                ->required(fn (Get $get) => filled($get($question->key)))
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get, $question) {
                        $otp = OneTimePassword::find($get($question->key . '_otp_id'));
                        if ($otp->phone !== $get($question->key) || ! $otp->verify($value)) {
                            $fail('The :attribute is invalid.');
                        }
                    },
                ]),
        ];
    }

    protected function getNumberComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question)
            ->numeric()
            ->when(
                $question->settings['number']['min'] ?? null,
                fn (TextInput $component, string $value) => $component
                    ->minValue($value)
            )
            ->when(
                $question->settings['number']['max'] ?? null,
                fn (TextInput $component, string $value) => $component
                    ->maxValue($value)
            );
    }

    protected function getUrlComponent(SurveyQuestion $question): TextInput
    {
        return $this->makeTextInputComponent($question)
            ->url();
    }

    protected function getPhotoComponent(SurveyQuestion $question, bool $isPreview = false): FileUpload
    {
        return FileUpload::make($question->key)
            ->avatar()
            ->directory('survey/photos/' . $question->key)
            ->imageEditor(fn (FileUpload $component) => ! $component->isDisabled())
            ->imageEditorAspectRatios(['1:1'])
            ->imageEditorMode(2)
            ->label($question->text)
            ->panelAspectRatio('1:1')
            ->panelLayout('compact')
            ->required($question->is_required)
            ->storeFiles(! $isPreview);
    }
}
