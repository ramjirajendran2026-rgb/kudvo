<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MemberResource\Pages;
use App\Forms\MemberForm;
use App\Models\Member;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentClusters\Forms\Cluster;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    protected static ?string $recordTitleAttribute = 'display_name';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        MemberForm::membershipNumberComponent()
                            ->when(
                                Filament::getTenant(),
                                callback: fn (TextInput $component) => $component
                                    ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('organisation_id', Filament::getTenant()->getKey()))
                            ),

                        Cluster::make(schema: [
                            MemberForm::titleComponent()
                                ->placeholder(placeholder: __('filament.user.elector-resource.form.title.placeholder')),

                            MemberForm::firstNameComponent()
                                ->columnSpan(2)
                                ->placeholder(placeholder: __('filament.user.elector-resource.form.first_name.placeholder')),

                            MemberForm::lastNameComponent()
                                ->columnSpan(2)
                                ->placeholder(placeholder: __('filament.user.elector-resource.form.last_name.placeholder')),
                        ])
                            ->columns(columns: 5)
                            ->label(label: __('filament.user.elector-resource.form.full_name.label')),

                        MemberForm::emailComponent(),

                        MemberForm::phoneComponent()
                            ->defaultCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                            ->disableLookup()
                            ->initialCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country')),

                        MemberForm::isActiveComponent(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: '#')
                    ->rowIndex(),

                TextColumn::make(name: 'membership_number')
                    ->badge()
                    ->label(label: __('filament.user.elector-resource.table.membership_number.label'))
                    ->searchable(),

                TextColumn::make(name: 'full_name')
                    ->label(label: __('filament.user.elector-resource.table.full_name.label'))
                    ->searchable()
                    ->wrap(),

                TextColumn::make(name: 'phone')
                    ->label(label: __('filament.user.elector-resource.table.phone.label'))
                    ->searchable(),

                TextColumn::make(name: 'email')
                    ->label(label: __('filament.user.elector-resource.table.email.label'))
                    ->wrap()
                    ->searchable(),

                Tables\Columns\ToggleColumn::make(name: 'is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ]);
    }
}
