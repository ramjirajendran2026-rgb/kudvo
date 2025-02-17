<?php

namespace App\Filament\Imports;

use App\Forms\Components\CountryPicker;
use App\Models\Participant;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ParticipantImporter extends Importer
{
    protected static ?string $model = Participant::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make(name: 'name')
                ->example(example: 'ABC Corp.')
                ->requiredMapping()
                ->rules(rules: ['required', 'max:150']),

            ImportColumn::make(name: 'membership_number')
                ->example(example: 'MEM-12345')
                ->rules(rules: fn (array $options, Participant $record): array => [
                    'max:150',
                    Rule::unique(table: 'participants')
                        ->ignoreModel(model: $record)
                        ->where(column: 'meeting_id', value: $options['meeting_id'] ?? null),
                ]),

            ImportColumn::make(name: 'email')
                ->example(example: 'abc@example.com')
                ->rules(rules: ['email', 'max:150']),

            ImportColumn::make(name: 'phone')
                ->castStateUsing(callback: fn (?string $state, array $options) => ($phone = phone(number: $state, country: Arr::wrap(value: $options['phone_country']))) && $phone->isValid() ? $phone->formatE164() : $state)
                ->example(example: '+919876543210')
                ->fillRecordUsing(callback: fn (?string $state, array $options, Participant $record) => $record->phone = phone(number: $state, country: Arr::wrap(value: $options['phone_country']))->isValid() ? phone(number: $state, country: Arr::wrap(value: $options['phone_country']))->formatE164() : null)
                ->rules(rules: ['max:150', 'phone:AUTO']),

            ImportColumn::make(name: 'weightage')
                ->example(example: '20')
                ->numeric()
                ->rules(rules: ['numeric', 'min:0']),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            CountryPicker::make(name: 'phone_country')
                ->required(),
        ];
    }

    public function resolveRecord(): ?Participant
    {
        return Participant::firstOrNew([
            'meeting_id' => $this->getOptions()['meeting_id'],

            'membership_number' => $this->data['membership_number'],
        ]);
    }

    protected function beforeValidate(): void
    {
        data_fill(target: $this->data, key: 'phone_country', value: $this->getOptions()['phone_country']);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your participant import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
