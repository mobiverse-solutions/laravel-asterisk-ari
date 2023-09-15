<?php

namespace Mobiverse\LaravelAsteriskAri;

use Illuminate\Support\ServiceProvider;

/**
 * @package Mobiverse\LaravelAsteriskAri
 * Laravel Asterisk ARI service provider class
 */
class LaravelAsteriskAriServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
            __DIR__ . '/../config/laravel-asterisk-ari.php' => config_path('laravel-asterisk-ari.php'),
            ],
            'config'
        );

        $this->registerCommands();
    }

    /**
     * Register the package commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands(
            [
                Console\Commands\StartServer::class,
            ]
        );
    }
}
