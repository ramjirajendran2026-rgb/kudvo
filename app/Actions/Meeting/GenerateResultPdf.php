<?php

namespace App\Actions\Meeting;

use App\Models\Meeting;
use App\Models\ResolutionVote;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class GenerateResultPdf
{
    public function execute(Meeting $meeting)
    {
        $meeting->loadMissing('participants.votes');

        return Pdf::view('pdf.meeting.resolution.result.index', [
            'meeting' => $meeting,
            'organisation' => $meeting->organisation,
            'resolutionVotes' => ResolutionVote::whereRelation('resolution', 'meeting_id', $meeting->getKey())->get(),
        ])
            ->footerView('pdf.meeting.resolution.result.footer', ['meeting' => $meeting])
            ->format(Format::A4)
            ->margins(10, 10, 10, 10)
            ->name('result-of-' . Str::slug($meeting->name) . '.pdf');
    }
}
