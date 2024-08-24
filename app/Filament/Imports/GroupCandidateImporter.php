<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;

class GroupCandidateImporter extends CandidateImporter
{
    public static function getColumns(): array
    {
        return [
            ...parent::getColumns(),

            ImportColumn::make(name: 'group.short_name')
                ->example(example: 'Team A')
                ->exampleHeader(header: 'Group')
                ->label(label: 'Group')
                ->relationship(
                    name: 'candidateGroup',
                    resolveUsing: 'short_name',
                )
                ->rules(rules: ['max:100']),
        ];
    }
}
