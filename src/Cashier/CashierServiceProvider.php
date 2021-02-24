<?php

namespace Cashier;

use Laravel\Cashier\Cashier;
use Laravel\Cashier\CashierServiceProvider as ServiceProvider;
use Stripe\Stripe;
use Unitable\GrahamStripe\Cashier\StripeSubscription;
use Unitable\GrahamStripe\Cashier\StripeSubscriptionItem;
use Unitable\GrahamStripe\GrahamStripe;

class CashierServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        parent::register();

        Cashier::useSubscriptionModel(StripeSubscription::class);
        Cashier::useSubscriptionItemModel(StripeSubscriptionItem::class);
    }

    /**
     * Setup the configuration for Cashier.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/graham-stripe.php', 'cashier'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $this->registerLogger();
        $this->registerResources();

        Stripe::setAppInfo(
            'Graham Stripe',
            GrahamStripe::VERSION,
            'https://github.com/unitable/graham-stripe'
        );
    }

}
