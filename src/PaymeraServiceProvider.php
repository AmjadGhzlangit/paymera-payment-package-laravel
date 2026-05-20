<?php

namespace Casper\Paymera;

use Illuminate\Support\ServiceProvider;

class PaymeraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/paymera.php', 'paymera');

        $this->app->singleton('paymera', function ($app) {
            return new PaymeraClient($app['config']['paymera']);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/paymera.php' => config_path('paymera.php'),
        ], 'paymera-config');
    }
}
