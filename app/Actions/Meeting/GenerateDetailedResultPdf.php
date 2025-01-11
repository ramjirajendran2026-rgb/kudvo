<?php

namespace App\Actions\Meeting;

use App\Models\Meeting;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class GenerateDetailedResultPdf
{
    public function execute(Meeting $meeting)
    {
        $meeting->loadMissing('participants.votes');

        return Pdf::view('pdf.meeting.resolution.detailed-result.index', [
            'meeting' => $meeting,
            'organisation' => $meeting->organisation,
        ])
            ->footerView('pdf.meeting.resolution.detailed-result.footer', ['meeting' => $meeting])
            ->format(Format::A4)
            ->margins(10, 10, 10, 10)
            ->name('detailed-result-of-' . Str::slug($meeting->name) . '.pdf');
    }
}
