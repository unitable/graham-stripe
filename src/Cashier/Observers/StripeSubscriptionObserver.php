<?php

namespace Unitable\GrahamStripe\Cashier\Observers;

use Unitable\GrahamStripe\Cashier\Events\StripeSubscriptionCreated;
use Unitable\GrahamStripe\Cashier\Events\StripeSubscriptionUpdated;
use Unitable\GrahamStripe\Cashier\StripeSubscription;

class StripeSubscriptionObserver {

    /**
     * Handle the Stripe subscription "created" event.
     *
     * @param StripeSubscription $stripe_subscription
     * @return void
     */
    public function created(StripeSubscription $stripe_subscription) {
        StripeSubscriptionCreated::dispatch($stripe_subscription);
    }

    /**
     * Handle the Stripe subscription "updated" event.
     *
     * @param StripeSubscription $stripe_subscription
     * @return void
     */
    public function updated(StripeSubscription $stripe_subscription) {
        StripeSubscriptionUpdated::dispatch($stripe_subscription);
    }

}
