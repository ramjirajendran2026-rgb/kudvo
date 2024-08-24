<?php

namespace App\Filament\Imports;

use App\Models\CandidateGroup;
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
                ->guess(['group'])
                ->label(label: 'Group')
                ->relationship(
                    name: 'candidateGroup',
                    resolveUsing: function (?string $state, array $options): ?CandidateGroup {
                        if (blank($state)) {
                            return null;
                        }

                        return CandidateGroup::query()
                            ->where('election_id', $options['election_id'])
                            ->where('short_name', $state)
                            ->firstOrCreate([
                                'name' => $state,
                                'short_name' => $state,
                                'election_id' => $options['election_id'],
                            ]);
                    }
                )
                ->rules(rules: ['max:100']),
        ];
    }
}
