<?php

namespace Unitable\GrahamStripe\Engine\Listeners;

use Unitable\GrahamStripe\Cashier\Events\StripeSubscriptionUpdated;

class UpdateSubscription {

    /**
     * Handle the event.
     *
     * @param StripeSubscriptionUpdated $event
     * @return void
     */
    public function handle(StripeSubscriptionUpdated $event) {

    }

}
