<?php

namespace App\Filament\User\Resources;

use App\Filament\Imports\MemberImporter;
use App\Filament\User\Resources\MemberResource\Pages;
use App\Forms\MemberForm;
use App\Models\Member;
use Database\Factories\MemberFactory;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
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

    public static function getGenerateDummyMembersAction(): Action
    {
        return Action::make('generateDummyMembers')
            ->authorize(auth()->user()->hasAdminRole())
            ->requiresConfirmation()
            ->action(function (Action $action, array $data) {
                Member::factory($data['count'])
                    ->for(Filament::getTenant(), 'organisation')
                    ->when($data['branch_id'], fn (MemberFactory $factory) => $factory->state(['branch_id' => $data['branch_id']]))
                    ->when($data['with_name'], fn (MemberFactory $factory) => $factory->withName())
                    ->when($data['with_email'], fn (MemberFactory $factory) => $factory->withEmail())
                    ->when($data['with_phone'], fn (MemberFactory $factory) => $factory->withPhone())
                    ->when($data['with_weightage'], fn (MemberFactory $factory) => $factory->withWeightage())
                    ->create();

                $action->success();
            })
            ->form([
                TextInput::make('count')
                    ->default(10)
                    ->integer()
                    ->maxValue(99999)
                    ->minValue(1)
                    ->required(),

                BranchResource::getFormSelectTree(),

                Toggle::make('with_name')
                    ->default(true),

                Toggle::make('with_email')
                    ->default(true),

                Toggle::make('with_phone')
                    ->default(true),

                Toggle::make('with_weightage')
                    ->default(true),
            ])
            ->successNotificationTitle('Generated successfully');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        BranchResource::getFormSelectTree(),

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

                        MemberForm::weightageComponent(),

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

                TextColumn::make(name: 'branch.name')
                    ->wrap(),

                TextColumn::make(name: 'membership_number')
                    ->color('primary')
                    ->copyable()
                    ->fontFamily('mono')
                    ->icon(icon: 'heroicon-o-clipboard-document')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->label(label: __('filament.user.elector-resource.table.membership_number.label'))
                    ->searchable()
                    ->size(TextColumn\TextColumnSize::Large)
                    ->weight(FontWeight::SemiBold),

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

                TextColumn::make(name: 'weightage')
                    ->fontFamily('mono')
                    ->numeric()
                    ->weight(FontWeight::SemiBold),

                Tables\Columns\ToggleColumn::make(name: 'is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                BranchResource::getFilterComponent(),
            ]);
    }

    public static function getImportAction(): ImportAction
    {
        return ImportAction::make()
            ->chunkSize(size: 50)
            ->color(color: 'gray')
            ->icon(icon: 'heroicon-s-arrow-up-tray')
            ->importer(importer: MemberImporter::class)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->options(static fn () => ['organisation_id' => Filament::getTenant()?->getKey()]);
    }
}
