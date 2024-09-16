<?php

namespace App\Livewire\Pages;

use App\Models\QsyssMeetingRegistration;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

#[Layout('components.layouts.base')]
#[Title('Membership Registration Responses')]
class QsyssMeetingRegistrationResponses extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                PhoneColumn::make('phone')
                    ->displayFormat(PhoneInputNumberType::INTERNATIONAL)
                    ->searchable()
                    ->url(fn (QsyssMeetingRegistration $record) => 'tel:' . $record->phone),

                TextColumn::make('address')
                    ->wrap()
                    ->size(TextColumn\TextColumnSize::Small),

                TextColumn::make('postal_code')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->alignCenter()
                    ->date()
                    ->description(fn (QsyssMeetingRegistration $record) => $record->created_at->format('h:i A'))
                    ->label('Registered on')
                    ->sortable()
                    ->timezone('Asia/Kolkata'),
            ])
            ->heading('QSYSS Meeting Responses')
            ->query(QsyssMeetingRegistration::query());
    }
}
