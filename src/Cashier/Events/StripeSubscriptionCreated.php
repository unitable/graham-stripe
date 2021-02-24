<?php

namespace Unitable\GrahamStripe\Cashier\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Unitable\GrahamStripe\Cashier\StripeSubscription;

class StripeSubscriptionCreated {

    use Dispatchable;

    /**
     * The event subscription.
     *
     * @var StripeSubscription
     */
    public StripeSubscription $stripe_subscription;

    /**
     * Create a new event instance.
     *
     * @param StripeSubscription $stripe_subscription
     */
    public function __construct(StripeSubscription $stripe_subscription) {
        $this->stripe_subscription = $stripe_subscription;
    }

}
