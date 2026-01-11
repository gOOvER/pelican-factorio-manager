<?php

namespace gOOvER\FactorioManager\Filament\Server\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use gOOvER\FactorioManager\Helpers\VisibilityHelper;
use gOOvER\FactorioManager\Services\FactorioRconProvider;

class Players extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-users';

    protected string $view = 'factorio-manager::filament.server.pages.players';

    protected static ?int $navigationSort = 1;

    protected static string|\UnitEnum|null $navigationGroup = 'Factorio';

    public string $statusFilter = 'online';

    public static function getNavigationLabel(): string
    {
        return __('factorio-manager::messages.navigation_label');
    }

    public function getTitle(): string
    {
        return __('factorio-manager::messages.pages.list');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return VisibilityHelper::shouldShow();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('factorio-manager::messages.actions.refresh'))
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
        $provider = app(FactorioRconProvider::class);
        
        $allPlayers = $provider->getAllPlayers($serverId);
        $admins = $provider->getAdmins($serverId);
        $banned = $provider->getBannedPlayers($serverId);
        
        // Case-insensitive admin names (lowercase for comparison)
        $adminNames = array_map('strtolower', array_column($admins, 'name'));
        
        // Get whitelist
        $whitelist = $provider->getWhitelist($serverId);
        $whitelistNames = array_map('strtolower', array_column($whitelist, 'name'));
        
        $bannedData = [];
        foreach ($banned as $b) {
            $bannedData[strtolower($b['name'])] = $b['reason'] ?? '';
        }

        $players = [];
        foreach ($allPlayers as $player) {
            $playerNameLower = strtolower($player['name']);
            
            $players[] = [
                'name' => $player['name'],
                'online' => $player['online'] ?? false,
                'is_admin' => in_array($playerNameLower, $adminNames, true),
                'is_banned' => isset($bannedData[$playerNameLower]),
                'ban_reason' => $bannedData[$playerNameLower] ?? '',
                'is_whitelisted' => in_array($playerNameLower, $whitelistNames, true),
            ];
        }

        // Apply filter
        return match ($this->statusFilter) {
            'online' => array_values(array_filter($players, fn ($p) => $p['online'] === true)),
            'offline' => array_values(array_filter($players, fn ($p) => $p['online'] === false)),
            'admin' => array_values(array_filter($players, fn ($p) => $p['is_admin'] === true)),
            'banned' => array_values(array_filter($players, fn ($p) => $p['is_banned'] === true)),
            'whitelisted' => array_values(array_filter($players, fn ($p) => $p['is_whitelisted'] === true)),
            default => $players,
        };
    }

    /**
     * Get all admins (for separate admin list)
     * Uses correct case from player list
     */
    public function getAdminList(): array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return [];
        }

        $provider = app(FactorioRconProvider::class);
        $admins = $provider->getAdmins($server->uuid);
        $allPlayers = $provider->getAllPlayers($server->uuid);
        
        // Build case-correct name map from player list
        $correctNames = [];
        foreach ($allPlayers as $player) {
            $correctNames[strtolower($player['name'])] = $player['name'];
        }
        
        // Return admins with correct case names
        return array_map(function ($admin) use ($correctNames) {
            $lowerName = strtolower($admin['name']);
            return [
                'name' => $correctNames[$lowerName] ?? $admin['name']
            ];
        }, $admins);
    }

    /**
     * Get all whitelisted players (for separate whitelist)
     * Uses correct case from player list
     */
    public function getWhitelistList(): array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return [];
        }

        $provider = app(FactorioRconProvider::class);
        $whitelist = $provider->getWhitelist($server->uuid);
        $allPlayers = $provider->getAllPlayers($server->uuid);
        
        // Build case-correct name map from player list
        $correctNames = [];
        foreach ($allPlayers as $player) {
            $correctNames[strtolower($player['name'])] = $player['name'];
        }
        
        // Return whitelist with correct case names
        return array_map(function ($player) use ($correctNames) {
            $lowerName = strtolower($player['name']);
            return [
                'name' => $correctNames[$lowerName] ?? $player['name']
            ];
        }, $whitelist);
    }

    public function promotePlayer(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->promotePlayer($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-manager::messages.actions.promote.notify'))
            ->success()
            ->send();
        
        // Refresh the component to update the player list
        $this->dispatch('$refresh');
    }

    public function demotePlayer(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->demotePlayer($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-manager::messages.actions.demote.notify'))
            ->success()
            ->send();
        
        // Refresh the component to update the player list
        $this->dispatch('$refresh');
    }

    public function kickPlayer(string $playerName, string $reason = ''): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->kickPlayer($server->uuid, $playerName, $reason ?: __('factorio-manager::messages.actions.kick.default_reason'));
        
        Notification::make()
            ->title(__('factorio-manager::messages.actions.kick.notify'))
            ->success()
            ->send();
        
        // Refresh the component to update the player list
        $this->dispatch('$refresh');
    }

    public function banPlayer(string $playerName, string $reason = ''): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->banPlayer($server->uuid, $playerName, $reason ?: __('factorio-manager::messages.actions.ban.default_reason'));
        
        Notification::make()
            ->title(__('factorio-manager::messages.actions.ban.notify'))
            ->success()
            ->send();
        
        // Refresh the component to update the player list
        $this->dispatch('$refresh');
    }

    public function unbanPlayer(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->unbanPlayer($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-manager::messages.actions.unban.notify'))
            ->success()
            ->send();
        
        // Refresh the component to update the player list
        $this->dispatch('$refresh');
    }

    public function whitelistAdd(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->whitelistAdd($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-manager::messages.actions.whitelist_add.notify'))
            ->success()
            ->send();
        
        $this->dispatch('$refresh');
    }

    public function whitelistRemove(string $playerName): void
    {
        $server = Filament::getTenant();
        if (!$server) return;

        $provider = app(FactorioRconProvider::class);
        $provider->whitelistRemove($server->uuid, $playerName);
        
        Notification::make()
            ->title(__('factorio-manager::messages.actions.whitelist_remove.notify'))
            ->success()
            ->send();
        
        $this->dispatch('$refresh');
    }
}
