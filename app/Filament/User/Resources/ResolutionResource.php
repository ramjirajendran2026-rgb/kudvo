<?php

namespace App\Filament\User\Resources;

use App\Enums\ResolutionChoice;
use App\Models\Resolution;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class ResolutionResource extends Resource
{
    protected static ?string $model = Resolution::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isDiscovered = false;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema(components: [
                static::getNameFormComponent(),

                static::getDescriptionFormComponent(),

                static::getAllowAbstainVotesFormComponent(),

                Fieldset::make(label: 'Labels')
                    ->columns(columns: 3)
                    ->schema([
                        static::getForLabelFormComponent(),

                        static::getAgainstLabelFormComponent(),

                        static::getAbstainLabelFormComponent(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                Stack::make(schema: [
                    TextColumn::make(name: 'name')
                        ->weight(weight: FontWeight::SemiBold)
                        ->size(size: TextColumn\TextColumnSize::Large),

                    TextColumn::make(name: 'description')
                        ->extraAttributes(attributes: [
                            'class' => 'prose max-w-full lg:prose-lg',
                        ])
                        ->html(),
                ]),
            ])
            ->paginated(condition: false)
            ->recordTitleAttribute(attribute: static::getRecordTitleAttribute());
    }

    public static function getNameFormComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->charCounter(count: 255)
            ->maxLength(length: 255)
            ->required();
    }

    public static function getDescriptionFormComponent(): TiptapEditor
    {
        return TiptapEditor::make(name: 'description');
    }

    public static function getAllowAbstainVotesFormComponent(): Toggle
    {
        return Toggle::make(name: 'allow_abstain_votes')
            ->default(state: true)
            ->live();
    }

    protected static function getOptionLabelFormComponent(ResolutionChoice $option): TextInput
    {
        return TextInput::make(name: $option->value . '_label')
            ->datalist(options: $option->getLabelSuggestions())
            ->hiddenLabel()
            ->maxLength(length: 50)
            ->placeholder(placeholder: $option->getLabel())
            ->prefixIcon(icon: $option->getIcon());
    }

    public static function getForLabelFormComponent(): TextInput
    {
        return static::getOptionLabelFormComponent(option: ResolutionChoice::For);
    }

    public static function getAgainstLabelFormComponent(): TextInput
    {
        return static::getOptionLabelFormComponent(option: ResolutionChoice::Against);
    }

    public static function getAbstainLabelFormComponent(): TextInput
    {
        return static::getOptionLabelFormComponent(option: ResolutionChoice::Abstain)
            ->visible(condition: fn (Get $get): bool => $get('allow_abstain_votes'));
    }

    public static function getCreateTableAction(): CreateAction
    {
        return CreateAction::make()
            ->modalWidth(width: MaxWidth::FourExtraLarge)
            ->slideOver();
    }

    public static function getEditTableAction(): EditAction
    {
        return EditAction::make()
            ->iconButton()
            ->modalWidth(width: MaxWidth::FourExtraLarge)
            ->slideOver();
    }

    public static function getDeleteTableAction(): DeleteAction
    {
        return DeleteAction::make()
            ->iconButton();
    }
}
