<?php

namespace abdelrhmanSaeed\JwtGuard\ServiceProviders;

use abdelrhmanSaeed\JwtGuard\Auth\Guards\Jwt;
use Illuminate\Support\Facades\Auth;
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

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Auth::extend('jwt', function ($app, $name, array $config) {
            new Jwt(Auth::createUserProvider($config['provider']));
        });
        
    }
}
