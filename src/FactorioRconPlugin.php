<?php

namespace gOOvER\FactorioRcon;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class FactorioRconPlugin implements Plugin, HasPluginSettings
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'factorio-rcon';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();
        
        // Discover Resources, Pages, and Widgets dynamically
        $panel->discoverResources(
            plugin_path($this->getId(), "src/Filament/$id/Resources"), 
            "gOOvER\\FactorioRcon\\Filament\\$id\\Resources"
        );
        $panel->discoverPages(
            plugin_path($this->getId(), "src/Filament/$id/Pages"), 
            "gOOvER\\FactorioRcon\\Filament\\$id\\Pages"
        );
        $panel->discoverWidgets(
            plugin_path($this->getId(), "src/Filament/$id/Widgets"), 
            "gOOvER\\FactorioRcon\\Filament\\$id\\Widgets"
        );
    }

    public function boot(Panel $panel): void
    {
        // Register Views (views are auto-registered under plugin namespace)
        \Illuminate\Support\Facades\View::addNamespace(
            'factorio-rcon', 
            plugin_path('factorio-rcon', 'resources/views')
        );

        // Register widgets for Server panel only
        if ($panel->getId() === 'server') {
            \App\Filament\Server\Pages\Console::registerCustomWidgets(
                \App\Enums\ConsoleWidgetPosition::AboveConsole, 
                [\gOOvER\FactorioRcon\Filament\Server\Widgets\ServerStatusWidget::class]
            );
        }
    }

    public function getSettingsForm(): array
    {
        return [
            Toggle::make('rcon_enabled')
                ->label(__('factorio-rcon::messages.settings.rcon_enabled'))
                ->helperText(__('factorio-rcon::messages.settings.rcon_enabled_helper'))
                ->default(env('FACTORIO_RCON_ENABLED', true)),
            TextInput::make('nav_sort')
                ->label(__('factorio-rcon::messages.settings.nav_sort'))
                ->helperText(__('factorio-rcon::messages.settings.nav_sort_helper'))
                ->numeric()
                ->default(env('FACTORIO_RCON_NAV_SORT', 2)),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'FACTORIO_RCON_ENABLED' => $data['rcon_enabled'],
            'FACTORIO_RCON_NAV_SORT' => $data['nav_sort'],
        ]);

        Notification::make()
            ->title(__('factorio-rcon::messages.settings.saved'))
            ->success()
            ->send();
    }
}
