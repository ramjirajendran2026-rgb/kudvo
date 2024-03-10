<?php

namespace App\Filament\Election\Pages;

use App\Filament\Base\Widgets\ElectionStatsOverview;
use App\Filament\Base\Widgets\NonVotedElectors;
use App\Filament\Base\Widgets\VotedBallots;
use App\Filament\Election\Http\Middleware\EnsureStateIsAllowed;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class Monitor extends Page
{
    use InteractsWithElection;

    protected static string $view = 'filament.election.pages.monitor';

    protected static string | array $routeMiddleware = 'signed';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(Request $request, Agent $agent): void
    {
        abort_if(boolean: $agent->isRobot(), code: Response::HTTP_NOT_ACCEPTABLE);

        $token = $this->getElection()->monitorTokens()->firstWhere('key', $request->get(key: 'token'));

        abort_if(boolean: blank($token), code: Response::HTTP_UNAUTHORIZED);

        abort_if(
            boolean: $token->isActivated() &&
                Cookie::get(key: 'election_'.$this->getElection()->getKey().'_monitor_token') != $token->key,
            code: Response::HTTP_UNAUTHORIZED
        );

        if (! $token->isActivated()) {
            $token->ip_address = $request->ip();
            $token->user_agent = $request->userAgent();
            $token->touch(attribute: 'activated_at');

            Cookie::queue(
                Cookie::forever(name: 'election_'.$this->getElection()->getKey().'_monitor_token', value: $token->key)
            );
        }
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string|array
    {
        return [
            EnsureStateIsAllowed::class,
            ...$panel->getAuthMiddleware()
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ElectionStatsOverview::class,
//            ElectionVotingSummary::class,
            VotedBallots::class,
            NonVotedElectors::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getDownloadAction(),
        ];
    }

    protected function getDownloadAction(): Action
    {
        return Action::make(name: 'download')
            ->label(label: 'Download Result')
            ->visible($this->getElection()->is_completed)
            ->action(action: function (self $livewire) {
                $election = $livewire->getElection();

                $pdf = Pdf::loadView(
                    'pdf.election.result',
                    [
                        'election' => $election,
                    ],
                    [],
                    'UTF-8'
                );

                return response()
                    ->streamDownload(
                        callback: function () use ($pdf) {
                            echo $pdf->output();
                        },
                        name: "result-{$this->getElection()->code}.pdf",
                    );
            });
    }
}
