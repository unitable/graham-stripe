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
        $stripe_subscription = $event->stripe_subscription;

        if ($subscription = $stripe_subscription->subscription) {
            $subscription->status = $stripe_subscription->translateStatus();
            $subscription->trial_ends_at = $stripe_subscription->trial_ends_at;
            $subscription->period_ends_at = $stripe_subscription->period_ends_at;
            $subscription->ends_at = $stripe_subscription->ends_at;

            $subscription->save();
        }
    }

}
