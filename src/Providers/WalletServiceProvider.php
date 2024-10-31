<?php

namespace Towoju5\Wallet\Providers;

use Illuminate\Support\ServiceProvider;
use Towoju5\Wallet\Facades\WalletFacade;
use Towoju5\Wallet\Services\WalletService;

class WalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('wallet', function () {
            return new WalletService();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../database/migrations/' => database_path('migrations'),
                __DIR__.'/../../config/wallet.php' => config_path('wallet.php'),
            ], 'wallet-migrations');
        }
    }
}
