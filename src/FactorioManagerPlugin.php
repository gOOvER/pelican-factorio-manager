<?php

namespace gOOvER\FactorioManager;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class FactorioManagerPlugin implements Plugin, HasPluginSettings
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'factorio-manager';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();
        
        // Discover Resources, Pages, and Widgets dynamically
        $panel->discoverResources(
            plugin_path($this->getId(), "src/Filament/$id/Resources"), 
            "gOOvER\\FactorioManager\\Filament\\$id\\Resources"
        );
        $panel->discoverPages(
            plugin_path($this->getId(), "src/Filament/$id/Pages"), 
            "gOOvER\\FactorioManager\\Filament\\$id\\Pages"
        );
        $panel->discoverWidgets(
            plugin_path($this->getId(), "src/Filament/$id/Widgets"), 
            "gOOvER\\FactorioManager\\Filament\\$id\\Widgets"
        );
    }

    public function boot(Panel $panel): void
    {
        // Register Views (views are auto-registered under plugin namespace)
        \Illuminate\Support\Facades\View::addNamespace(
            'factorio-manager', 
            plugin_path('factorio-manager', 'resources/views')
        );

        // Register widgets for Server panel only
        if ($panel->getId() === 'server') {
            \App\Filament\Server\Pages\Console::registerCustomWidgets(
                \App\Enums\ConsoleWidgetPosition::AboveConsole, 
                [\gOOvER\FactorioManager\Filament\Server\Widgets\ServerStatusWidget::class]
            );
        }
    }

    public function getSettingsForm(): array
    {
        return [
            Toggle::make('rcon_enabled')
                ->label(__('factorio-manager::messages.settings.rcon_enabled'))
                ->helperText(__('factorio-manager::messages.settings.rcon_enabled_helper'))
                ->default(env('FACTORIO_RCON_ENABLED', true)),
            TextInput::make('nav_sort')
                ->label(__('factorio-manager::messages.settings.nav_sort'))
                ->helperText(__('factorio-manager::messages.settings.nav_sort_helper'))
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
            ->title(__('factorio-manager::messages.settings.saved'))
            ->success()
            ->send();
    }
}
