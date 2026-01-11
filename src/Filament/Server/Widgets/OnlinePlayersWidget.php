<?php

namespace gOOvER\FactorioManager\Filament\Server\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use gOOvER\FactorioManager\Services\FactorioRconProvider;
use gOOvER\FactorioManager\Helpers\VisibilityHelper;

class OnlinePlayersWidget extends BaseWidget
{
    // Auto-refresh every 10 seconds
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return [];
        }

        $provider = app(FactorioRconProvider::class);
        $status = $provider->getServerStatus($server->uuid);

        $onlineCount = 0;
        if (isset($status['players'])) {
            $onlineCount = count(array_filter($status['players'], fn($p) => $p['online'] ?? false));
        }

        return [
            Stat::make(__('factorio-manager::messages.widget.online_players'), $onlineCount)
                ->description(__('factorio-manager::messages.widget.server_status') . ': ' . 
                    ($status['online'] ? __('factorio-manager::messages.values.online') : __('factorio-manager::messages.values.offline')))
                ->color($status['online'] ? 'success' : 'danger')
                ->icon('heroicon-o-users'),
        ];
    }

    public static function canView(): bool
    {
        return VisibilityHelper::shouldShow();
    }
}
