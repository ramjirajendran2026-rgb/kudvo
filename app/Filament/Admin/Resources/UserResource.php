<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make(name: 'name')
                            ->maxLength(length: 255),

                        TextInput::make(name: 'email')
                            ->email()
                            ->maxLength(length: 255)
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                Impersonate::make()
                    ->redirectTo(redirectTo: fn (User $record): string => match (true) {
                        $record->hasAdminRole(),
                        $record->hasStaffRole() => Filament::getPanel(id: 'admin')->getUrl(),
                        default => Filament::getPanel(id: 'app')->getUrl()
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('mark_as_verified')
                        ->requiresConfirmation()
                        ->action(function (User $record, Tables\Actions\Action $action) {
                            $record->markEmailAsVerified();

                            $action->success();
                        })
                        ->successNotificationTitle('Marked as verified')
                        ->visible(fn (User $record): bool => ! $record->hasVerifiedEmail()),
                ]),
            ])
            ->columns(components: [
                Tables\Columns\TextColumn::make(name: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make(name: 'email')
                    ->description(description: fn (User $record): ?string => $record->name)
                    ->icon(icon: fn (User $record): ?string => $record->hasVerifiedEmail() ? 'heroicon-m-shield-check' : null)
                    ->iconColor(color: 'primary')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->searchable(condition: ['email', 'name']),

                Tables\Columns\TextColumn::make(name: 'roles.name')
                    ->badge(),

                Tables\Columns\TextColumn::make(name: 'created_at')
                    ->alignCenter()
                    ->date()
                    ->description(description: fn (User $record): string => $record->created_at->format(format: Table::$defaultTimeDisplayFormat)),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make(name: 'email_verified_at')
                    ->label(label: 'Email verified?'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route(path: '/'),
            'create' => Pages\CreateUser::route(path: '/create'),
            'edit' => Pages\EditUser::route(path: '/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }
}
