<?php

namespace App\Filament\Election\Http\Responses\Auth;

use App\Actions\Election\Booth\UpdateOnElectorLogout;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;

class LogoutResponse implements Responsable
{
    public function toResponse($request)
    {
        UpdateOnElectorLogout::execute();

        return redirect()->to(
            Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl(),
        );
    }
}
