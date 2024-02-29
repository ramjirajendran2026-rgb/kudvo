<?php

namespace App\Filament\Election\Pages;

use App\Facades\Kudvo;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\Base\Contracts\HasElector;
use App\Filament\Election\ElectionPanel;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Election\Pages\Concerns\InteractsWithElector;
use App\Models\Election;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use function Filament\authorize;

/**
 * @property Form $form
 */
abstract class BasePage extends Page implements HasElector, HasElection
{
    use InteractsWithElector;
    use InteractsWithElection;

    public bool $mock;

    public function mount(Request $request): void
    {
        $this->mock = $request->query(key: 'mock', default: false);
    }

    public static function can(string $action)
    {
        try {
            return authorize(action: $action, model: Kudvo::getElection() ?? Election::class)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    public function getPanel(): ElectionPanel
    {
        /** @var ElectionPanel $panel */
        $panel = Filament::getCurrentPanel();

        return $panel;
    }

    public function isSpa(): bool
    {
        return Filament::getCurrentPanel()->hasSpaMode();
    }

    public function isMock(): bool
    {
        return $this->mock;
    }

    public function getRedirectUrl(): ?string
    {
        return Index::getUrl(parameters: $this->isMock() ? ['mock' => 1] : []);
    }
}
