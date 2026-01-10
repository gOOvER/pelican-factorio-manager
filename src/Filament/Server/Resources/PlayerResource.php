<?php

namespace gOOvER\FactorioRcon\Filament\Server\Resources;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use gOOvER\FactorioRcon\Models\Player;
use gOOvER\FactorioRcon\Services\FactorioRconProvider;
use gOOvER\FactorioRcon\Filament\Server\Resources\PlayerResource\Pages;
use gOOvER\FactorioRcon\Helpers\VisibilityHelper;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-users';

    protected static ?int $navigationSort = 2;

    // Disable tenant ownership - Player is a virtual model without database relationship
    protected static bool $isScopedToTenant = false;

    public static function getNavigationLabel(): string
    {
        return __('factorio-rcon::messages.navigation_label');
    }

    public static function getNavigationSort(): int
    {
        return (int) env('FACTORIO_RCON_NAV_SORT', 2);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return VisibilityHelper::shouldShow();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('factorio-rcon::messages.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('online')
                    ->label(__('factorio-rcon::messages.columns.status'))
                    ->boolean()
                    ->trueIcon('tabler-circle-check')
                    ->falseIcon('tabler-circle-x')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label(__('factorio-rcon::messages.columns.admin'))
                    ->boolean()
                    ->trueIcon('tabler-shield-check')
                    ->falseIcon('tabler-shield')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_banned')
                    ->label(__('factorio-rcon::messages.filters.banned'))
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'all' => __('factorio-rcon::messages.filters.all'),
                        'online' => __('factorio-rcon::messages.filters.online'),
                        'offline' => __('factorio-rcon::messages.filters.offline'),
                        'admin' => __('factorio-rcon::messages.filters.admin'),
                        'banned' => __('factorio-rcon::messages.filters.banned'),
                    ])
                    ->default('online'),
            ])
            ->recordActions([
                Action::make('promote')
                    ->label(fn ($record) => $record->is_admin 
                        ? __('factorio-rcon::messages.actions.demote.label') 
                        : __('factorio-rcon::messages.actions.promote.label'))
                    ->icon(fn ($record) => $record->is_admin 
                        ? 'tabler-shield-x' 
                        : 'tabler-shield-check')
                    ->color(fn ($record) => $record->is_admin ? 'warning' : 'success')
                    ->button()
                    ->action(function ($record) {
                        $server = Filament::getTenant();
                        if (!$server) return;

                        $provider = app(FactorioRconProvider::class);
                        
                        if ($record->is_admin) {
                            $provider->demotePlayer($server->uuid, $record->name);
                            Notification::make()
                                ->title(__('factorio-rcon::messages.actions.demote.notify'))
                                ->success()
                                ->send();
                        } else {
                            $provider->promotePlayer($server->uuid, $record->name);
                            Notification::make()
                                ->title(__('factorio-rcon::messages.actions.promote.notify'))
                                ->success()
                                ->send();
                        }
                    })
                    ->requiresConfirmation(),

                Action::make('kick')
                    ->label(__('factorio-rcon::messages.actions.kick.label'))
                    ->icon('tabler-door-exit')
                    ->color('danger')
                    ->button()
                    ->form([
                        TextInput::make('reason')
                            ->label(__('factorio-rcon::messages.actions.kick.reason'))
                            ->default(__('factorio-rcon::messages.actions.kick.default_reason')),
                    ])
                    ->action(function ($record, array $data) {
                        $server = Filament::getTenant();
                        if (!$server) return;

                        $provider = app(FactorioRconProvider::class);
                        $provider->kickPlayer($server->uuid, $record->name, $data['reason'] ?? '');
                        
                        Notification::make()
                            ->title(__('factorio-rcon::messages.actions.kick.notify'))
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Action::make('ban')
                    ->label(__('factorio-rcon::messages.actions.ban.label'))
                    ->icon('tabler-ban')
                    ->color('danger')
                    ->button()
                    ->visible(fn ($record) => !$record->is_banned)
                    ->form([
                        TextInput::make('reason')
                            ->label(__('factorio-rcon::messages.actions.ban.reason'))
                            ->default(__('factorio-rcon::messages.actions.ban.default_reason')),
                    ])
                    ->action(function ($record, array $data) {
                        $server = Filament::getTenant();
                        if (!$server) return;

                        $provider = app(FactorioRconProvider::class);
                        $provider->banPlayer($server->uuid, $record->name, $data['reason'] ?? '');
                        
                        Notification::make()
                            ->title(__('factorio-rcon::messages.actions.ban.notify'))
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Action::make('unban')
                    ->label(__('factorio-rcon::messages.actions.unban.label'))
                    ->icon('tabler-circle-check')
                    ->color('success')
                    ->button()
                    ->visible(fn ($record) => $record->is_banned)
                    ->action(function ($record) {
                        $server = Filament::getTenant();
                        if (!$server) return;

                        $provider = app(FactorioRconProvider::class);
                        $provider->unbanPlayer($server->uuid, $record->name);
                        
                        Notification::make()
                            ->title(__('factorio-rcon::messages.actions.unban.notify'))
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('online', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayers::route('/'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $server = Filament::getTenant();
        if (!$server) {
            return parent::getEloquentQuery();
        }

        $serverId = $server->uuid;
        $provider = new FactorioRconProvider();
        
        $allPlayers = $provider->getAllPlayers($serverId);
        $admins = $provider->getAdmins($serverId);
        $banned = $provider->getBannedPlayers($serverId);
        
        $adminNames = array_column($admins, 'name');
        $bannedNames = array_column($banned, 'name');

        $players = [];
        foreach ($allPlayers as $player) {
            $players[] = new Player([
                'name' => $player['name'],
                'online' => $player['online'] ?? false,
                'is_admin' => in_array($player['name'], $adminNames),
                'is_banned' => in_array($player['name'], $bannedNames),
            ]);
        }

        return Player::query()->whereIn('id', array_map(fn($p) => $p->name, $players));
    }
}
