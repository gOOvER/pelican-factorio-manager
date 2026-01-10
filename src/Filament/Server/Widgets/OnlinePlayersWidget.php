<?php

namespace gOOvER\FactorioRcon\Filament\Server\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use gOOvER\FactorioRcon\Services\FactorioRconProvider;
use gOOvER\FactorioRcon\Helpers\VisibilityHelper;

class OnlinePlayersWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return [];
        }

        $provider = new FactorioRconProvider();
        $status = $provider->getServerStatus($server->uuid);

        $onlineCount = 0;
        if (isset($status['players'])) {
            $onlineCount = count(array_filter($status['players'], fn($p) => $p['online'] ?? false));
        }

        return [
            Stat::make(__('factorio-rcon::messages.widget.online_players'), $onlineCount)
                ->description(__('factorio-rcon::messages.widget.server_status') . ': ' . 
                    ($status['online'] ? __('factorio-rcon::messages.values.online') : __('factorio-rcon::messages.values.offline')))
                ->color($status['online'] ? 'success' : 'danger')
                ->icon('heroicon-o-users'),
        ];
    }

    public static function canView(): bool
    {
        return VisibilityHelper::shouldShow();
    }
}
