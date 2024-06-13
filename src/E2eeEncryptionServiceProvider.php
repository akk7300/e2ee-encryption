<?php

namespace Akk7300\E2eeEncryption;

use Akk7300\E2eeEncryption\Commands\GenerateKeyPairsCommand;
use Illuminate\Support\ServiceProvider;

class E2eeEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/e2ee_encryption.php' => config_path('e2ee_encryption.php'),
            ], 'config');

            $this->commands([
                GenerateKeyPairsCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/e2ee_encryption.php', 'e2ee_encryption');
    }
}
