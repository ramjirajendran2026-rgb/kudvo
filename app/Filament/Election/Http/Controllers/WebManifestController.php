<?php

namespace App\Filament\Election\Http\Controllers;

use App\Data\WebAppManifestIconData;
use App\Enums\WebAppManifestDisplay;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\Index;
use App\Http\Controllers\Controller;
use App\Models\Election;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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

        $data->icons?->map(function (WebAppManifestIconData $icon) use ($election) {
            $icon->src = Storage::url(path: $icon->src);

            return $icon;
        });

        return response()->json(data: $data);
    }
}
