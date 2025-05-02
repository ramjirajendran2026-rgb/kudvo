<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BranchResource\Pages;
use App\Filament\User\Resources\BranchResource\Widgets\Branches;
use App\Models\Branch;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-bold';

    protected static ?string $activeNavigationIcon = 'heroicon-s-bold';

    public static function canViewAny(): bool
    {
        return Filament::getTenant()?->settings?->allow_branches ?? false;
    }

    public static function getFormSelectTree(): SelectTree
    {
        return SelectTree::make('branch_id')
            ->enableBranchNode()
            ->label('Branch')
            ->relationship(
                'branch',
                'display_name',
                'parent_id',
                fn ($query) => $query->where('organisation_id', Filament::getTenant()?->getKey())
            )
            ->required()
            ->searchable()
            ->withCount()
            ->visible(condition: fn () => Filament::getTenant()?->settings?->allow_branches ?? false);
    }

    public static function getFilterComponent(): Filter
    {
        return Filter::make('branch')
            ->form([
                SelectTree::make('id')
                    ->enableBranchNode()
                    ->label('Branch')
                    ->relationship(
                        'branch',
                        'display_name',
                        'parent_id',
                        fn (Builder $query) => $query->where('organisation_id', Filament::getTenant()?->getKey())
                    )
                    ->withCount(),
            ])
            ->query(
                fn (Builder $query, array $data) => $query
                    ->when(
                        $data['id'] ?? null,
                        fn (Builder $query, $value) => $query->where('branch_id', $value)
                    )
            );
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('organisation_id', Filament::getTenant()->getKey())),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                SelectTree::make('parent_id')
                    ->enableBranchNode()
                    ->label('Parent')
                    ->relationship(
                        'parent',
                        'display_name',
                        'parent_id',
                        fn (Builder $query) => $query->where('organisation_id', Filament::getTenant()?->getKey())
                    )
                    ->searchable()
                    ->withCount(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBranches::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Branches::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false);
    }
}
