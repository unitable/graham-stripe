<?php

namespace Unitable\GrahamStripe;

use Illuminate\Support\ServiceProvider;
use Unitable\GrahamStripe\Engines\Stripe\StripeEngine;

class GrahamStripeServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'graham-stripe');

        // Register the main class to use with the facade
        $this->app->singleton('graham-stripe', function() {
            return new GrahamStripe;
        });

        $this->app->singleton(StripeEngine::class, function() {
            return new StripeEngine();
        });
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'graham-stripe');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'graham-stripe');
         $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('graham-stripe.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/graham-stripe'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/graham-stripe'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/graham-stripe'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

}
