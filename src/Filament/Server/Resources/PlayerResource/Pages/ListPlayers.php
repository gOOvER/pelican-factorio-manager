<?php

namespace gOOvER\FactorioRcon\Filament\Server\Resources\PlayerResource\Pages;

use Filament\Resources\Pages\ListRecords;
use gOOvER\FactorioRcon\Filament\Server\Resources\PlayerResource;
use Filament\Facades\Filament;
use gOOvER\FactorioRcon\Services\FactorioRconProvider;
use gOOvER\FactorioRcon\Models\Player;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPlayers extends ListRecords
{
    protected static string $resource = PlayerResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('factorio-rcon::messages.pages.list');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTableRecords(): Collection|Paginator
    {
        $server = Filament::getTenant();
        if (!$server) {
            return collect([]);
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
            $players[] = new Player([
                'name' => $player['name'],
                'online' => $player['online'] ?? false,
                'is_admin' => in_array($player['name'], $adminNames),
                'is_banned' => isset($bannedData[$player['name']]),
                'ban_reason' => $bannedData[$player['name']] ?? '',
            ]);
        }

        $collection = collect($players);
        
        // Apply status filter
        $statusFilter = $this->getTableFilterState('status')['value'] ?? 'online';
        
        $collection = match ($statusFilter) {
            'online' => $collection->filter(fn ($player) => $player->online === true),
            'offline' => $collection->filter(fn ($player) => $player->online === false),
            'admin' => $collection->filter(fn ($player) => $player->is_admin === true),
            'banned' => $collection->filter(fn ($player) => $player->is_banned === true),
            default => $collection, // 'all' shows everything
        };
        
        // Return paginated results
        return new LengthAwarePaginator(
            $collection->values(),
            $collection->count(),
            50, // per page
            1,  // current page
            ['path' => request()->url()]
        );
    }
}