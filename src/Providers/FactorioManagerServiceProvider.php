<?php

namespace gOOvER\FactorioManager\Providers;

use Illuminate\Support\ServiceProvider;
use gOOvER\FactorioManager\Services\FactorioRconProvider;

class FactorioManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register config
        $this->mergeConfigFrom(
            plugin_path('factorio-manager', 'config/factorio-manager.php'),
            'factorio-manager'
        );

        // Register singleton for connection reuse
        $this->app->singleton(FactorioRconProvider::class, function () {
            return new FactorioRconProvider();
        });
    }

    public function boot(): void
    {
        // Publish config if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                plugin_path('factorio-manager', 'config/factorio-manager.php') => config_path('factorio-manager.php'),
            ], 'factorio-manager-config');
        }

        // Close all RCON connections at the end of each request
        $this->app->terminating(function () {
            FactorioRconProvider::closeAllConnections();
        });
    }
}
