<?php

namespace gOOvER\FactorioRcon\Providers;

use Illuminate\Support\ServiceProvider;

class FactorioRconServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register config
        $this->mergeConfigFrom(
            plugin_path('factorio-rcon', 'config/factorio-rcon.php'),
            'factorio-rcon'
        );
    }

    public function boot(): void
    {
        // Publish config if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                plugin_path('factorio-rcon', 'config/factorio-rcon.php') => config_path('factorio-rcon.php'),
            ], 'factorio-rcon-config');
        }
    }
}
