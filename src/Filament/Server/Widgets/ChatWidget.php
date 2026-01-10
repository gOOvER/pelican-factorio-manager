<?php

namespace gOOvER\FactorioRcon\Filament\Server\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use gOOvER\FactorioRcon\Services\FactorioRconProvider;
use gOOvER\FactorioRcon\Helpers\VisibilityHelper;

class ChatWidget extends Widget
{
    protected string $view = 'factorio-rcon::filament.server.widgets.chat';

    protected int | string | array $columnSpan = 'full';

    public $message = '';
    public $recipient = 'all';

    public function sendMessage(): void
    {
        if (empty($this->message)) {
            \Filament\Notifications\Notification::make()
                ->title(__('factorio-rcon::messages.chat.empty_message'))
                ->danger()
                ->send();
            return;
        }

        $server = Filament::getTenant();
        if (!$server) {
            \Filament\Notifications\Notification::make()
                ->title(__('factorio-rcon::messages.chat.server_not_found'))
                ->danger()
                ->send();
            return;
        }

        $provider = new FactorioRconProvider();
        
        if ($this->recipient === 'all') {
            $success = $provider->sendMessage($server->uuid, $this->message);
        } else {
            $success = $provider->whisperPlayer($server->uuid, $this->recipient, $this->message);
        }

        if ($success) {
            \Filament\Notifications\Notification::make()
                ->title(__('factorio-rcon::messages.chat.message_sent'))
                ->success()
                ->send();
            
            $this->message = '';
        } else {
            \Filament\Notifications\Notification::make()
                ->title(__('factorio-rcon::messages.chat.message_failed'))
                ->danger()
                ->send();
        }
    }

    public function getOnlinePlayers(): array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return [];
        }

        $provider = new FactorioRconProvider();
        $players = $provider->getOnlinePlayers($server->uuid);
        
        return array_filter($players, fn($p) => $p['online'] ?? false);
    }

    public static function canView(): bool
    {
        return VisibilityHelper::shouldShow();
    }
}
