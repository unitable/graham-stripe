<?php

namespace Unitable\GrahamStripe\Engine;

use Illuminate\Support\ServiceProvider;

class StripeEngineServiceProvider extends ServiceProvider {

    /**
     * Register the application services.
     */
    public function register() {
        $this->app->singleton(StripeEngine::class, function() {
            return new StripeEngine();
        });
    }

    /**
     * Bootstrap the package services.
     */
    public function boot() {
        //
    }

}
