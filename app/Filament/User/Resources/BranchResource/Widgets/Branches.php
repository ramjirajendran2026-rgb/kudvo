<?php

namespace App\Filament\User\Resources\BranchResource\Widgets;

use App\Models\Branch;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use SolutionForest\FilamentTree\Actions\DeleteAction;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Actions\ViewAction;
use SolutionForest\FilamentTree\Widgets\Tree as BaseWidget;

class Branches extends BaseWidget
{
    protected static string $model = Branch::class;

    protected static int $maxDepth = 2;

    protected ?string $treeTitle = 'Branches';

    protected bool $enableTreeTitle = true;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('code')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('organisation_id', Filament::getTenant()->getKey())),

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            SelectTree::make('parent_id')
                ->label('Parent')
                ->relationship('parent', 'display_name', 'parent_id'),
        ];
    }

    public function getViewFormSchema(): array
    {
        return [
            TextEntry::make('code'),
            TextEntry::make('display_name'),
            TextEntry::make('parent.display_name'),
        ];
    }

    protected function getTreeActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getTreeQuery(): Builder
    {
        return Branch::query()
            ->where('organisation_id', Filament::getTenant()?->getKey());
    }
}
