<?php

namespace App\Filament\Election\Http\Controllers;

use App\Data\WebAppManifestIconData;
use App\Enums\WebAppManifestDisplay;
use App\Filament\Election\Pages\Index;
use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class WebManifestController extends Controller
{
    public function __invoke(Election $election)
    {
        abort_unless(boolean: $election->isPwaEnabled(), code: Response::HTTP_NOT_FOUND);

        $data = $election->preference->web_app_manifest;
        $data->start_url = Index::getUrl();
        $data->id = "el/$election->code";
        $data->display = WebAppManifestDisplay::Standalone;

        $data->icons?->map(function (WebAppManifestIconData $icon) {
            $icon->src = Storage::disk(config('filament.default_filesystem_disk'))->url(path: $icon->src);

            return $icon;
        });

        return response()->json(data: $data);
    }
}
