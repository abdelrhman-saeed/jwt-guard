<?php

namespace abdelrhmanSaeed\JwtGuard\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class JwtGuardServiceProvider extends ServiceProvider
{

    public function register()
    {
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../Auth/Config/DefaultTokenConfig.php' => config_path('DefaultTokenConfig.php'),
            __DIR__ . '/../Auth/Config/AuthenticatorsConfig.php' => config_path('AuthenticatorsConfig.php'),
        ]);
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        
    }
}
