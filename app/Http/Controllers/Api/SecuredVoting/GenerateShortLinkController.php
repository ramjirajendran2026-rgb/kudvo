<?php

namespace App\Http\Controllers\Api\SecuredVoting;

use App\Http\Controllers\Controller;
use App\Models\ShortLink;
use Illuminate\Http\Request;

class GenerateShortLinkController extends Controller
{
    public function __invoke(Request $request)
    {
        if (
            ! $request->filled('api_key') ||
            $request->get('api_key') != config('services.secured_voting.api_key')
        ) {
            return 'Unauthorized';
        }

        $data = $request->validate([
            'destination' => 'required|max:1000|url',
        ]);

        return route('short_link.go', [ShortLink::create($data)->key]);
    }
}
