<?php

namespace App\Filament\Imports;

use App\Filament\User\Resources\BranchResource;
use App\Forms\Components\CountryPicker;
use App\Models\Member;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Illuminate\Validation\Rule;

class MemberImporter extends Importer
{
    protected static ?string $model = Member::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make(name: 'membership_number')
                ->example(example: 'MEM1001')
                ->requiredMapping()
                ->rules(rules: fn (array $options, Member $record): array => [
                    'required', 'max:50',
                    Rule::unique(table: 'members')
                        ->ignoreModel(model: $record)
                        ->where(column: 'organisation_id', value: $options['organisation_id']),
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
                    callback: fn (?string $state, array $options, Member $record) => $record->phone = ($phone = phone(number: $state, country: $options['phone_country']))->isValid() ? $phone->formatE164() : null
                )
                ->rules(rules: fn (array $options, Member $record): array => [
                    'nullable',
                    'phone:AUTO',
                    Rule::unique(table: 'members')
                        ->ignoreModel(model: $record)
                        ->where(column: 'organisation_id', value: $options['organisation_id']),
                ]),

            ImportColumn::make(name: 'email')
                ->example(example: 'mem1001@association.com')
                ->rules(rules: fn (array $options, Member $record): array => [
                    'nullable', 'email', 'max:100',
                    Rule::unique(table: 'members')
                        ->ignoreModel(model: $record)
                        ->where(column: 'organisation_id', value: $options['organisation_id']),
                ]),

            ImportColumn::make(name: 'password')
                ->example('<PASSWORD>')
                ->ignoreBlankState()
                ->rules(rules: ['nullable', 'string', 'max:60']),

            ImportColumn::make(name: 'weightage')
                ->example(example: '1')
                ->numeric()
                ->rules(rules: ['nullable', 'numeric', 'min:0.00000001']),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your member import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            BranchResource::getFormSelectTree()
                ->inlineLabel(),

            CountryPicker::make(name: 'phone_country')
                ->default(state: fn () => Filament::getTenant()?->country)
                ->inlineLabel()
                ->label(label: 'Default phone country'),
        ];
    }

    public function resolveRecord(): ?Member
    {
        return Member::firstOrNew(
            [
                'organisation_id' => $this->options['organisation_id'],
                'membership_number' => $this->data['membership_number'],
            ],
            [
                'branch_id' => $this->options['branch_id'] ?? null,
            ],
        );
    }
}
