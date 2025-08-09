<?php

namespace App\Actions\Election;

use App\Models\Election;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class GenerateResultPdf
{
    public function execute(Election $election, ?int $boothId = null)
    {
        return Pdf::view('pdf.election.result.index', [
            'election' => $election,
            'organisation' => $election->organisation,
            'boothId' => $boothId,
        ])
            ->footerView('pdf.election.result.footer', ['election' => $election])
            ->format(Format::A4)
            ->margins(10, 10, 10, 10)
            ->name('result-of-' . Str::slug($election->name) . '.pdf')
            ->withBrowsershot(function (Browsershot $browsershot) {
                return $browsershot
                    ->setIncludePath('$PATH:"/Users/smiliyas/Library/Application Support/Herd/config/nvm/versions/node/v22.16.0/bin"')
                    ->waitUntilNetworkIdle()
                    ->emulateMedia('print')
                    ->addChromiumArguments([
                        '--disable-web-security',
                        '--font-render-hinting=none',
                        '--disable-font-subpixel-positioning',
                        '--disable-lcd-text',
                    ]);
            });
    }
}
