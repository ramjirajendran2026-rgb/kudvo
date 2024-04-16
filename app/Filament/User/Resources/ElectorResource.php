<?php

namespace App\Filament\User\Resources;

use App\Filament\Base\Contracts\HasElection;
use App\Filament\Imports\ElectorImporter;
use App\Forms\ElectorForm;
use App\Models\Election;
use App\Models\Elector;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\ChunkIterator;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ImportAction as ImportTableAction;
use Filament\Tables\Actions\ImportAction as TableImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentClusters\Forms\Cluster;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Number;
use Illuminate\Validation\Rules\Unique;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ElectorResource extends Resource
{
    protected static ?string $model = Elector::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'membership_number';

    public static function form(Form $form): Form
    {
        $formLivewire = $form->getLivewire();

        return $form
            ->schema(components: [
                ElectorForm::membershipNumberComponent(),

                Cluster::make(schema: [
                    ElectorForm::titleComponent()
                        ->placeholder(placeholder: 'Title'),

                    ElectorForm::firstNameComponent()
                        ->columnSpan(2)
                        ->placeholder(placeholder: 'First name'),

                    ElectorForm::lastNameComponent()
                        ->columnSpan(2)
                        ->placeholder(placeholder: 'Last name'),
                ])
                    ->columns(columns: 5)
                    ->label(label: 'Full name'),

                ElectorForm::emailComponent()
                    ->when(
                        value: $formLivewire instanceof HasElection && !$formLivewire->getElection()->preference?->elector_duplicate_email,
                        callback: fn(TextInput $component) => $component
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn(Unique $rule, HasElection $livewire) => $rule
                                    ->where(column: 'event_type', value: Election::class)
                                    ->where(column: 'event_id', value: $livewire->getElection()->getKey())
                            )
                    ),

                ElectorForm::phoneComponent()
                    ->defaultCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                    ->disableIpLookUp()
                    ->initialCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                    ->when(
                        value: $formLivewire instanceof HasElection && !$formLivewire->getElection()->preference?->elector_duplicate_phone,
                        callback: fn(PhoneInput $component) => $component
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn(Unique $rule, HasElection $livewire) => $rule
                                    ->where(column: 'event_type', value: Election::class)
                                    ->where(column: 'event_id', value: $livewire->getElection()->getKey())
                            )
                    ),

                ElectorForm::groupsComponent()
                    ->hidden(),

                ElectorForm::segmentsComponent()
                    ->visible(condition: fn ($livewire) => $livewire instanceof HasElection && $livewire->getElection()->preference?->segmented_ballot),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                TextColumn::make(name: '#')
                    ->rowIndex(),

                TextColumn::make(name: 'membership_number')
                    ->badge()
                    ->label(label: 'Membership number')
                    ->searchable(),

                TextColumn::make(name: 'full_name')
                    ->label(label: 'Full name')
                    ->searchable()
                    ->wrap(),

                TextColumn::make(name: 'phone')
                    ->label(label: 'Phone number')
                    ->searchable(),

                TextColumn::make(name: 'email')
                    ->label(label: 'Email address')
                    ->wrap()
                    ->searchable(),

                TextColumn::make(name: 'segments.name')
                    ->badge()
                    ->visible(condition: fn ($livewire) => $livewire instanceof HasElection && $livewire->getElection()->preference?->segmented_ballot)
                    ->wrap(),
            ])
            ->headerActions(actions: [
                static::getTableImportAction(),

                static::getTableCreateAction(),
            ])
            ->recordTitleAttribute(attribute: static::getRecordTitleAttribute());
    }

    public static function getTableImportAction(): TableImportAction
    {
        return TableImportAction::make()
            ->color(color: 'gray')
            ->icon(icon: 'heroicon-s-arrow-up-tray')
            ->importer(importer: ElectorImporter::class)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->action(action: function (ImportAction|ImportTableAction $action, array $data) {
                /** @var TemporaryUploadedFile $csvFile */
                $csvFile = $data['file'];

                $csvStream = $action->getUploadedFileStream($csvFile);

                if (! $csvStream) {
                    return;
                }

                $csvReader = CsvReader::createFromStream($csvStream);

                if (filled($csvDelimiter = $action->getCsvDelimiter($csvReader))) {
                    $csvReader->setDelimiter($csvDelimiter);
                }

                $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);
                $csvResults = Statement::create()->process($csvReader);

                $totalRows = $csvResults->count();
                $maxRows = $action->getMaxRows() ?? $totalRows;

                if ($maxRows < $totalRows) {
                    Notification::make()
                        ->title(__('filament-actions::import.notifications.max_rows.title'))
                        ->body(trans_choice('filament-actions::import.notifications.max_rows.body', $maxRows, [
                            'count' => Number::format($maxRows),
                        ]))
                        ->danger()
                        ->send();

                    return;
                }

                $user = auth()->user();

                $import = app(Import::class);
                $import->user()->associate($user);
                $import->file_name = $csvFile->getClientOriginalName();
                $import->file_path = $csvFile->getRealPath();
                $import->importer = $action->getImporter();
                $import->total_rows = $totalRows;
                $import->save();

                $importChunkIterator = new ChunkIterator($csvResults->getRecords(), chunkSize: $action->getChunkSize());

                /** @var array<array<array<string, string>>> $importChunks */
                $importChunks = $importChunkIterator->get();

                $job = $action->getJob();

                $options = array_merge(
                    $action->getOptions(),
                    Arr::except($data, ['file', 'columnMap']),
                );

                // We do not want to send the loaded user relationship to the queue in job payloads,
                // in case it contains attributes that are not serializable, such as binary columns.
                $import->unsetRelation('user');

                $importJobs = collect($importChunks)
                    ->map(fn (array $importChunk): object => new ($job)(
                        $import,
                        rows: base64_encode(serialize($importChunk)),
                        columnMap: $data['columnMap'],
                        options: $options,
                    ));

                $importer = $import->getImporter(
                    columnMap: $data['columnMap'],
                    options: $options,
                );

                Bus::batch($importJobs->all())
                    ->allowFailures()
                    ->when(
                        filled($jobQueue = $importer->getJobQueue()),
                        fn (PendingBatch $batch) => $batch->onQueue($jobQueue),
                    )
                    ->when(
                        filled($jobConnection = $importer->getJobConnection()),
                        fn (PendingBatch $batch) => $batch->onConnection($jobConnection),
                    )
                    ->when(
                        filled($jobBatchName = $importer->getJobBatchName()),
                        fn (PendingBatch $batch) => $batch->name($jobBatchName),
                    )
                    ->finally(function () use ($import) {
                        $import->touch('completed_at');

                        if (! $import->user instanceof Authenticatable) {
                            return;
                        }

                        $failedRowsCount = $import->getFailedRowsCount();

                        Notification::make()
                            ->title(__('filament-actions::import.notifications.completed.title'))
                            ->body($import->importer::getCompletedNotificationBody($import))
                            ->when(
                                ! $failedRowsCount,
                                fn (Notification $notification) => $notification->success(),
                            )
                            ->when(
                                $failedRowsCount && ($failedRowsCount < $import->total_rows),
                                fn (Notification $notification) => $notification->warning(),
                            )
                            ->when(
                                $failedRowsCount === $import->total_rows,
                                fn (Notification $notification) => $notification->danger(),
                            )
                            ->when(
                                $failedRowsCount,
                                fn (Notification $notification) => $notification->actions([
                                    NotificationAction::make('downloadFailedRowsCsv')
                                        ->label(trans_choice('filament-actions::import.notifications.completed.actions.download_failed_rows_csv.label', $failedRowsCount, [
                                            'count' => Number::format($failedRowsCount),
                                        ]))
                                        ->color('danger')
                                        ->url(route('filament.imports.failed-rows.download', ['import' => $import], absolute: false), shouldOpenInNewTab: true)
                                        ->markAsRead(),
                                ]),
                            )
                            ->broadcast($import->user)
                            ->sendToDatabase($import->user);
                    })
                    ->dispatch();

                Notification::make()
                    ->title($action->getSuccessNotificationTitle())
                    ->body(trans_choice('filament-actions::import.notifications.started.body', $import->total_rows, [
                        'count' => Number::format($import->total_rows),
                    ]))
                    ->success()
                    ->send();
            });
    }

    public static function getTableCreateAction(): TableCreateAction
    {
        return TableCreateAction::make()
            ->createAnother(condition: false)
            ->form(form: fn(Form $form): Form => static::form(form: $form))
            ->icon(icon: 'heroicon-m-plus')
            ->model(model: static::getModel())
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modelLabel(label: static::getModelLabel())
            ->modalWidth(width: MaxWidth::Medium);
    }

    public static function getTableEditAction(): TableEditAction
    {
        return TableEditAction::make()
            ->form(form: fn(Form $form): Form => static::form($form))
            ->iconButton()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium);
    }

    public static function getTableDeleteAction(): TableDeleteAction
    {
        return TableDeleteAction::make()
            ->iconButton();
    }

    public static function getBulkDeleteAction(): DeleteBulkAction
    {
        return DeleteBulkAction::make();
    }
}
