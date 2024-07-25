<?php

namespace App\Filament\Imports;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

class CandidateImporter extends Importer
{
    protected static ?string $model = Candidate::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make(name: 'position')
                ->example(example: 'President')
                ->label(label: 'Position')
                ->relationship(
                    resolveUsing: fn (string $state, array $options) => Position::where('name->' . $options['locale'], $state)
                        ->where('event_type', Election::class)
                        ->where('event_id', $options['election_id'])
                        ->first(),
                )
                ->requiredMapping()
                ->rules(rules: ['max:100']),

            ImportColumn::make(name: 'membership_number')
                ->example(example: 'MEM-12345')
                ->label(label: 'Membership Number')
                ->rules(rules: fn (array $options) => [
                    'nullable',
                    'max:100',
                    Rule::exists(table: 'electors', column: 'membership_number')
                        ->where(column: 'event_type', value: Election::class)
                        ->where(column: 'event_id', value: $options['election_id']),
                ]),

            ImportColumn::make(name: 'title')
                ->example(example: 'Mr.')
                ->label(label: 'Salutation')
                ->rules(rules: ['max:10']),

            ImportColumn::make(name: 'first_name')
                ->example(example: 'John')
                ->label(label: 'First Name')
                ->requiredMapping()
                ->rules(rules: ['required', 'max:100']),

            ImportColumn::make(name: 'last_name')
                ->example(example: 'Doe')
                ->label(label: 'Last Name')
                ->rules(rules: ['max:100']),
        ];
    }

    public function resolveRecord(): ?Candidate
    {
        // return Candidate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Candidate();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your candidate import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
