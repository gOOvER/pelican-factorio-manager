<?php

namespace gOOvER\FactorioRcon\Filament\Server\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use gOOvER\FactorioRcon\Helpers\VisibilityHelper;
use gOOvER\FactorioRcon\Services\FactorioRconProvider;

class Players extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-users';

    protected static string $view = 'factorio-rcon::filament.server.pages.players';

    protected static ?int $navigationSort = 2;

    public string $statusFilter = 'online';

    public static function getNavigationLabel(): string
    {
        return __('factorio-rcon::messages.navigation_label');
    }

    public static function getNavigationSort(): int
    {
        return (int) env('FACTORIO_RCON_NAV_SORT', 2);
    }

    public function getTitle(): string
    {
        return __('factorio-rcon::messages.pages.list');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return VisibilityHelper::shouldShow();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('factorio-rcon::messages.actions.refresh'))
                ->icon('tabler-refresh')
                ->color('gray')
                ->action(fn () => null),
        ];
    }

    public function getPlayers(): array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return [];
        }

        $serverId = $server->uuid;
        $provider = new FactorioRconProvider();
        
        $allPlayers = $provider->getAllPlayers($serverId);
        $admins = $provider->getAdmins($serverId);
        $banned = $provider->getBannedPlayers($serverId);
        
        $adminNames = array_column($admins, 'name');
        $bannedData = [];
        foreach ($banned as $b) {
            $bannedData[$b['name']] = $b['reason'] ?? '';
        }

        $players = [];
        foreach ($allPlayers as $player) {
            $players[] = [
                'name' => $player['name'],
                'online' => $player['online'] ?? false,
                'is_admin' => in_array($player['name'], $adminNames),
                'is_banned' => isset($bannedData[$player['name']]),
                'ban_reason' => $bannedData[$player['name']] ?? '',
            ];
        }

        // Apply filter
        return match ($this->statusFilter) {
            'online' => array_values(array_filter($players, fn ($p) => $p['online'] === true)),
            'offline' => array_values(array_filter($players, fn ($p) => $p['online'] === false)),
            'admin' => array_values(array_filter($players, fn ($p) => $p['is_admin'] === true)),
            'banned' => array_values(array_filter($players, fn ($p) => $p['is_banned'] === true)),
            default => $players,
        };
    }

    public function promotePlayer(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->promotePlayer($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-rcon::messages.actions.promote.notify'))
            ->success()
            ->send();
    }

    public function demotePlayer(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->demotePlayer($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-rcon::messages.actions.demote.notify'))
            ->success()
            ->send();
    }

    public function kickPlayer(string $playerName, string $reason = ''): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->kickPlayer($server->uuid, $playerName, $reason ?: __('factorio-rcon::messages.actions.kick.default_reason'));
        
        Notification::make()
            ->title(__('factorio-rcon::messages.actions.kick.notify'))
            ->success()
            ->send();
    }

    public function banPlayer(string $playerName, string $reason = ''): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->banPlayer($server->uuid, $playerName, $reason ?: __('factorio-rcon::messages.actions.ban.default_reason'));
        
        Notification::make()
            ->title(__('factorio-rcon::messages.actions.ban.notify'))
            ->success()
            ->send();
    }

    public function unbanPlayer(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->unbanPlayer($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-rcon::messages.actions.unban.notify'))
            ->success()
            ->send();
    }
}
