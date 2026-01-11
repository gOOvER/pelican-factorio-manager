<?php

namespace gOOvER\FactorioManager\Filament\Server\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use gOOvER\FactorioManager\Services\FactorioRconProvider;
use gOOvER\FactorioManager\Helpers\VisibilityHelper;

class ServerStatusWidget extends Widget
{
    protected string $view = 'factorio-manager::filament.server.widgets.server-status';

    protected int | string | array $columnSpan = 'full';

    // Auto-refresh every 10 seconds
    protected ?string $pollingInterval = '10s';

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

        $provider = app(FactorioRconProvider::class);
        return $provider->getServerStatus($server->uuid);
    }

    public static function canView(): bool
    {
        return VisibilityHelper::shouldShow();
    }
}
