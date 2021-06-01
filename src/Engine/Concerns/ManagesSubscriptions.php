<?php

namespace Unitable\GrahamStripe\Engine\Concerns;

use Unitable\Graham\Method\Method;
use Unitable\Graham\Plan\Plan;
use Unitable\Graham\Plan\PlanPrice;
use Unitable\Graham\Subscription\Subscription;
use Unitable\GrahamStripe\Cashier\StripeSubscription;
use Unitable\GrahamStripe\Engine\SubscriptionBuilder;

trait ManagesSubscriptions {

    /**
     * Create a new subscription.
     *
     * @param $owner
     * @param Plan $plan
     * @param Method $method
     * @param PlanPrice|null $plan_price
     * @return SubscriptionBuilder
     */
    public function newSubscription($owner, Plan $plan, Method $method, ?PlanPrice $plan_price = null): SubscriptionBuilder {
        return new SubscriptionBuilder($owner, $plan, $method, $plan_price);
    }

    /**
     * Cancel a subscription.
     *
     * @param Subscription $subscription
     * @return void
     */
    public function cancelSubscription(Subscription $subscription) {
        $stripe_subscription = StripeSubscription::findBySubscriptionId($subscription->id);

        if (!$stripe_subscription)
            throw new \RuntimeException('Stripe subscription not found.');

        $stripe_subscription->noProrate()->cancel();
    }

    /**
     * Cancel a subscription immediately.
     *
     * @param Subscription $subscription
     */
    public function cancelSubscriptionImmediately(Subscription $subscription) {
        $stripe_subscription = StripeSubscription::findBySubscriptionId($subscription->id);

        if (!$stripe_subscription)
            throw new \RuntimeException('Stripe subscription not found.');

        $stripe_subscription->noProrate()->cancelNow();
    }

    /**
     * Resume a subscription.
     *
     * @param Subscription $subscription
     */
    public function resumeSubscription(Subscription $subscription) {
        $stripe_subscription = StripeSubscription::findBySubscriptionId($subscription->id);

        if (!$stripe_subscription)
            throw new \RuntimeException('Stripe subscription not found.');

        $stripe_subscription->resume();
    }

    /**
     * Modify a subscription.
     *
     * @param Subscription $subscription
     * @param array $data
     */
    public function modifySubscription(Subscription $subscription, array $data) {
        throw new \LogicException('Not implemented.');
    }

}
