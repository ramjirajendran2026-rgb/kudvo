<?php

namespace App\Exports;

use App\Actions\Survey\GenerateReferenceNumber;
use App\Enums\SurveyQuestionType;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SurveyResponsesExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping
{
    protected GenerateReferenceNumber $generateReferenceNumber;

    public function __construct(
        protected Survey $survey,
    ) {
        $this->generateReferenceNumber = app(GenerateReferenceNumber::class);
    }

    public function collection(): Collection
    {
        return SurveyResponse::whereBelongsTo($this->survey)
            ->with('answers')
            ->get()
            ->map(
                fn (SurveyResponse $response) => collect(['reference_number' => $this->generateReferenceNumber->execute($response, $this->survey), 'sort' => $response->id, 'created_at' => $response->created_at->format('Y-m-d H:i:s')])
                    ->merge($response->answers->mapWithKeys(fn (SurveyAnswer $surveyAnswer) => [SurveyQuestion::KEY_PREFIX . $surveyAnswer->question_id => $this->getFormattedAnswer($surveyAnswer)])->toArray())
            );
    }

    public function map($row): array
    {
        return [
            $row['reference_number'] ?? 'N/A',
            Date::dateTimeToExcel(Carbon::parse($row['created_at']) ?? now()),
            ...$this->getQuestions()
                ->pluck('id')
                ->map(fn (int $questionId) => $row[SurveyQuestion::KEY_PREFIX . $questionId] ?? '')
                ->toArray(),
        ];
    }

    public function headings(): array
    {
        return ['Reference Number', 'Submitted At', ...$this->getQuestions()->pluck('text')->values()->toArray()];
    }

    protected function getQuestions()
    {
        return $this->survey->questions;
    }

    protected function getFormattedAnswer(SurveyAnswer $answer)
    {
        return match ($this->getQuestions()->find($answer->question_id)?->type) {
            SurveyQuestionType::Photo => Storage::disk(config('filament.default_filesystem_disk'))->url($answer->content),
            default => $answer->content_formatted,
        };
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
