<?php

namespace App\Filament\Imports;

use App\Forms\Components\CountryPicker;
use App\Models\Election;
use App\Models\Elector;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Illuminate\Validation\Rule;

class ElectorImporter extends Importer
{
    protected static ?string $model = Elector::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make(name: 'membership_number')
                ->example(example: 'MEM1001')
                ->requiredMapping()
                ->rules(rules: fn (array $options, Elector $record): array => [
                    'required', 'max:50',
                    Rule::unique(table: 'electors')
                        ->ignoreModel(model: $record)
                        ->where(column: 'event_type', value: $options['event_type'])
                        ->where(column: 'event_id', value: $options['event_id']),
                ]),

            ImportColumn::make(name: 'first_name')
                ->example(example: 'John')
                ->rules(rules: ['max:100']),

            ImportColumn::make(name: 'last_name')
                ->example(example: 'Doe')
                ->rules(rules: ['max:100']),

            ImportColumn::make(name: 'phone')
                ->castStateUsing(
                    callback: fn (?string $state, array $options): ?string => $state &&
                    ($phone = phone(number: $state, country: $options['phone_country'])) &&
                    $phone->isValid() ?
                        $phone->formatE164() :
                        $state
                )
                ->example(example: '9876543210')
                ->fillRecordUsing(
                    callback: fn (?string $state, array $options, Elector $record) => $record->phone = ($phone = phone(number: $state, country: $options['phone_country']))->isValid() ? $phone->formatE164() : null
                )
                ->rules(rules: function (array $options, Elector $record): array {
                    $rules = [
                        'nullable',
                        //                        'phone:INTERNATIONAL,'.($options['phone_country'] ?? '')
                    ];

                    if ($options['event_type'] === Election::class && Election::find(id: $options['event_id'])?->preference->elector_duplicate_phone === false) {
                        $rules[] = Rule::unique(table: 'electors')
                            ->ignoreModel(model: $record)
                            ->where(column: 'event_type', value: $options['event_type'])
                            ->where(column: 'event_id', value: $options['event_id']);
                    }

                    return $rules;
                }),

            ImportColumn::make(name: 'email')
                ->example(example: 'mem1001@association.com')
                ->rules(rules: function (array $options, Elector $record): array {
                    $rules = ['nullable', 'email', 'max:100'];

                    if ($options['event_type'] === Election::class && Election::find(id: $options['event_id'])?->preference->elector_duplicate_email === false) {
                        $rules[] = Rule::unique(table: 'electors')
                            ->ignoreModel(model: $record)
                            ->where(column: 'event_type', value: $options['event_type'])
                            ->where(column: 'event_id', value: $options['event_id']);
                    }

                    return $rules;
                }),

            ImportColumn::make(name: 'groups')
                ->array()
                ->example(example: 'Life Member')
                ->fillRecordUsing(
                    callback: fn (?array $state, Elector $record) => $record->groups = filled(value: $state) ?
                        implode(separator: ',', array: $state) :
                        null
                )
                ->nestedRecursiveRules(rules: ['max:50'])
                ->rules(rules: ['array']),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            CountryPicker::make(name: 'phone_country')
                ->default(state: fn () => Filament::getTenant()?->country)
                ->inlineLabel()
                ->label(label: 'Default phone country'),
        ];
    }

    public function resolveRecord(): ?Elector
    {
        return Elector::firstOrNew([
            'event_type' => $this->options['event_type'],
            'event_id' => $this->options['event_id'],

            'membership_number' => $this->data['membership_number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your elector import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
