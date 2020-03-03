<?php
namespace Unmit\ldk;

use Illuminate\Support\ServiceProvider;

class LdkServiceProvider extends ServiceProvider {
    public function boot()
    {
        app('router')->aliasMiddleware('icas.auth', Http\Middleware\IntegratedCASAuth::class);
        app('router')->aliasMiddleware('api.oauth.handler', Http\Middleware\APIOAuthHandler::class);

        // Publish React objects
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'unmit');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/'),
            __DIR__.'/../resources/images' => resource_path('images/'),
            __DIR__.'/../resources/sass' => resource_path('unm_sass/'),
            __DIR__.'/../resources/js' => resource_path('js/'),
            __DIR__.'/../resources/js/components' => resource_path('js/components/')
        ]);
    }
    public function register()
    {
    }

}