<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use App\Enums\RolesEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('name')
                    ->enum(RolesEnum::class)
                    ->options(Arr::mapWithKeys(RolesEnum::cases(), fn (RolesEnum $case): array => [$case->value => $case->getLabel()]))
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->paginated(false)
            ->heading(null)
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelect(
                        fn (Forms\Components\Select $select) => $select
                            ->getOptionLabelFromRecordUsing(fn (Role $record) => RolesEnum::tryFrom($record->name)?->getLabel())
                    ),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->iconButton(),
            ]);
    }
}
