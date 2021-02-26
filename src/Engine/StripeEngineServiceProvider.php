<?php

namespace Unitable\GrahamStripe\Engine;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Unitable\GrahamStripe\Cashier\Events;
use Unitable\GrahamStripe\Engine\Listeners;

class StripeEngineServiceProvider extends ServiceProvider {

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton(StripeEngine::class, function() {
            return new StripeEngine();
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        $this->registerEvents();
    }

    /**
     * Register any application events.
     *
     * @return void
     */
    public function registerEvents() {
        Event::listen(Events\StripeSubscriptionUpdated::class, Listeners\UpdateSubscription::class);
        Event::listen(Events\StripeSubscriptionInvoiceUpdated::class, Listeners\UpdateSubscriptionInvoice::class);
    }

}
