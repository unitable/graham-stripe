<?php

namespace Unitable\GrahamStripe;

use Illuminate\Support\ServiceProvider;

class GrahamStripeServiceProvider extends ServiceProvider {

    /**
     * Register the application services.
     */
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/graham-stripe.php', 'graham-stripe');

        $this->app->singleton('graham-stripe', function() {
            return new GrahamStripe;
        });
    }

    /**
     * Bootstrap the package services.
     */
    public function boot() {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'graham-stripe');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'graham-stripe');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/graham-stripe.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/graham-stripe.php' => config_path('graham-stripe.php'),
            ], 'graham-config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/graham-stripe'),
            ], 'graham-views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/graham-stripe'),
            ], graham-assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/graham-stripe'),
            ], 'graham-lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

}
