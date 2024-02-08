<?php

namespace App\Filament\Election\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Filament\Facades\Filament;
use Symfony\Component\HttpFoundation\Response;

class WebManifestController extends Controller
{
    public function __invoke(Election $election)
    {
        abort_unless(boolean: $election->isPwaEnabled(), code: Response::HTTP_NOT_FOUND);

        $data = $election->web_app_manifest;
        $data->start_url = Filament::getUrl();

        return response()->json(data: $data);
    }
}
