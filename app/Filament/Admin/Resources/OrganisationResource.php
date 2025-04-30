<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrganisationResource\Pages;
use App\Filament\Admin\Resources\OrganisationResource\RelationManagers\UsersRelationManager;
use App\Forms\OrganisationForm;
use App\Models\Organisation;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrganisationResource extends Resource
{
    use Translatable;

    protected static ?string $model = Organisation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(5)
            ->schema([
                Section::make()
                    ->columnSpan(span: 4)
                    ->schema(components: [
                        OrganisationForm::nameComponent(),

                        OrganisationForm::countryComponent(),

                        OrganisationForm::timezoneComponent(),
                    ]),

                OrganisationForm::logoComponent()
                    ->hiddenLabel(),

                Section::make('Other settings')
                    ->columns(3)
                    ->columnSpanFull()
                    ->statePath('settings')
                    ->schema([
                        Toggle::make('allow_branches')
                            ->default(false),

                        Toggle::make('allow_members')
                            ->default(false),

                        Toggle::make('allow_elections')
                            ->default(true),

                        Toggle::make('allow_nominations')
                            ->default(true),

                        Toggle::make('allow_meetings')
                            ->default(true),

                        Toggle::make('allow_surveys')
                            ->default(true),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrganisations::route('/'),
            'edit' => Pages\EditOrganisation::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\TextColumn::make('timezone'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ]);
    }
}
