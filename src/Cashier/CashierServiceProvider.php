<?php

namespace Unitable\GrahamStripe\Cashier;

use Illuminate\Support\Facades\Config;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\CashierServiceProvider as ServiceProvider;
use Stripe\Stripe;
use Unitable\GrahamStripe\Cashier\Observers\StripeSubscriptionInvoiceObserver;
use Unitable\GrahamStripe\Cashier\Observers\StripeSubscriptionObserver;
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
        $this->registerObservers();

        Stripe::setAppInfo(
            'Graham Stripe',
            GrahamStripe::VERSION,
            'https://github.com/unitable/graham-stripe'
        );

        $this->overrideCashierConfig();
    }

    /**
     * Register any application observers.
     *
     * @return void
     */
    protected function registerObservers() {
        StripeSubscription::observe(StripeSubscriptionObserver::class);
        StripeSubscriptionInvoice::observe(StripeSubscriptionInvoiceObserver::class);
    }

    /**
     * Override Cashier config with package config.
     *
     * @return void
     */
    protected function overrideCashierConfig() {
        $configs = Config::get('graham-stripe');

        foreach ($configs as $key => $value) {
            Config::set('cashier.' . $key, $value);
        }
    }

}
