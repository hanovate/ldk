<?php
namespace Unmit\ldk;

use Illuminate\Support\ServiceProvider;

class LdkServiceProvider extends ServiceProvider {
    public function boot()
    {
        app('router')->aliasMiddleware('icas.auth', Http\Middleware\IntegratedCASAuth::class);
        app('router')->aliasMiddleware('api.oauth.handler', Http\Middleware\APIOAuthHandler::class);
    }
    public function register()
    {
    }

}