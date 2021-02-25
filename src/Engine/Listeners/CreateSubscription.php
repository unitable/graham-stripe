<?php

namespace Unitable\GrahamStripe\Engine\Listeners;

use Unitable\GrahamStripe\Cashier\Events\StripeSubscriptionCreated;

class CreateSubscription {

    /**
     * Handle the event.
     *
     * @param StripeSubscriptionCreated $event
     * @return void
     */
    public function handle(StripeSubscriptionCreated $event) {
        $stripe_subscription = $event->stripe_subscription;
        $stripe_object = $stripe_subscription->asStripeSubscription();

        dd($stripe_subscription, $stripe_object);
    }

}
