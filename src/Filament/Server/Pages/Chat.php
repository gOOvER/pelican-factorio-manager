<?php

namespace gOOvER\FactorioManager\Filament\Server\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use gOOvER\FactorioManager\Helpers\VisibilityHelper;
use gOOvER\FactorioManager\Services\FactorioRconProvider;

class Chat extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-message-circle';

    protected string $view = 'factorio-manager::filament.server.pages.chat';

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = 'Factorio';

    public ?array $data = [];

    // Auto-refresh every 5 seconds
    public function getPollingInterval(): ?string
    {
        return '5s';
    }

    public static function getNavigationLabel(): string
    {
        return __('factorio-manager::messages.pages.chat');
    }

    public function getTitle(): string
    {
        return __('factorio-manager::messages.pages.chat');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return VisibilityHelper::shouldShow();
    }

    public function mount(): void
    {
        $this->form->fill([
            'recipient' => 'all',
            'message' => '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('recipient')
                    ->label(__('factorio-manager::messages.chat.recipient'))
                    ->options(fn () => $this->getRecipientOptions())
                    ->default('all')
                    ->native(false)
                    ->searchable()
                    ->required(),
                TextInput::make('message')
                    ->label(__('factorio-manager::messages.chat.message'))
                    ->placeholder(__('factorio-manager::messages.chat.message_placeholder'))
                    ->maxLength(255)
                    ->required()
                    ->extraInputAttributes(['autofocus' => true]),
            ])
            ->statePath('data');
    }

    protected function getRecipientOptions(): array
    {
        $options = ['all' => __('factorio-manager::messages.chat.all_players')];

        $server = Filament::getTenant();
        if ($server) {
            $provider = app(FactorioRconProvider::class);
            $players = $provider->getOnlinePlayers($server->uuid);

            foreach ($players as $player) {
                if ($player['online'] ?? false) {
                    $options[$player['name']] = '👤 ' . $player['name'];
                }
            }
        }

        return $options;
    }

    public function sendMessage(): void
    {
        $data = $this->form->getState();

        if (empty($data['message'])) {
            Notification::make()
                ->title(__('factorio-manager::messages.chat.empty_message'))
                ->danger()
                ->send();
            return;
        }

        $server = Filament::getTenant();
        if (!$server) {
            Notification::make()
                ->title(__('factorio-manager::messages.chat.server_not_found'))
                ->danger()
                ->send();
            return;
        }

        $provider = app(FactorioRconProvider::class);

        if ($data['recipient'] === 'all') {
            $success = $provider->sendMessage($server->uuid, $data['message']);
        } else {
            $success = $provider->whisperPlayer($server->uuid, $data['recipient'], $data['message']);
        }

        if ($success) {
            Notification::make()
                ->title(__('factorio-manager::messages.chat.message_sent'))
                ->success()
                ->send();

            $this->form->fill([
                'recipient' => $data['recipient'],
                'message' => '',
            ]);
        } else {
            Notification::make()
                ->title(__('factorio-manager::messages.chat.message_failed'))
                ->danger()
                ->send();
        }
    }

    public function setQuickMessage(string $message): void
    {
        $this->form->fill([
            'recipient' => $this->data['recipient'] ?? 'all',
            'message' => $message,
        ]);
    }

    /**
     * Get chat log from the mod (if installed)
     */
    public function getChatLog(): ?array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return null;
        }

        $provider = app(FactorioRconProvider::class);
        return $provider->getChatLog($server->uuid);
    }

    /**
     * Check if chat log is available
     */
    public function isChatLogAvailable(): bool
    {
        $server = Filament::getTenant();
        if (!$server) {
            return false;
        }

        $provider = app(FactorioRconProvider::class);
        return $provider->isChatLogAvailable($server->uuid);
    }

    /**
     * Get extended server status from mod
     */
    public function getExtendedStatus(): ?array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return null;
        }

        $provider = app(FactorioRconProvider::class);
        return $provider->getExtendedServerStatus($server->uuid);
    }

    /**
     * Get detailed player list from mod
     */
    public function getDetailedPlayers(): ?array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return null;
        }

        $provider = app(FactorioRconProvider::class);
        return $provider->getDetailedOnlinePlayers($server->uuid);
    }

    /**
     * Clear chat log
     */
    public function clearChatLog(): void
    {
        $server = Filament::getTenant();
        if (!$server) {
            Notification::make()
                ->title(__('factorio-manager::messages.chat.server_not_found'))
                ->danger()
                ->send();
            return;
        }

        $provider = app(FactorioRconProvider::class);
        $success = $provider->clearChatLog($server->uuid);

        if ($success) {
            Notification::make()
                ->title(__('factorio-manager::messages.chat.log_cleared'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('factorio-manager::messages.chat.clear_failed'))
                ->danger()
                ->send();
        }
    }

    /**
     * Get mod version info
     */
    public function getModVersion(): ?array
    {
        $server = Filament::getTenant();
        if (!$server) {
            return null;
        }

        $provider = app(FactorioRconProvider::class);
        return $provider->getModVersion($server->uuid);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label(__('factorio-manager::messages.chat.send'))
                ->icon('tabler-send')
                ->action('sendMessage'),
        ];
    }
}
