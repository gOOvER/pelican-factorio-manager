<?php

namespace gOOvER\FactorioRcon\Filament\Server\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use gOOvER\FactorioRcon\Services\FactorioRconProvider;
use gOOvER\FactorioRcon\Helpers\VisibilityHelper;

class ServerStatusWidget extends Widget
{
    protected string $view = 'factorio-rcon::filament.server.widgets.server-status';

    protected int | string | array $columnSpan = 'full';

    public function getServerStatus(): array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return [
                'online' => false,
                'players' => [],
                'error' => 'Server not found'
            ];
        }

        $provider = new FactorioRconProvider();
        return $provider->getServerStatus($server->uuid);
    }

    public static function canView(): bool
    {
        return VisibilityHelper::shouldShow();
    }
}
