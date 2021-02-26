<?php

namespace Unitable\GrahamStripe\Cashier;

use Unitable\Graham\Contracts\DiscountMethod;
use Unitable\Graham\Subscription\Subscription;

class StripeDiscount implements DiscountMethod {

    /**
     * Get the discount for a subscription.
     *
     * @param Subscription $subscription
     * @return float
     */
    public function getDiscount(Subscription $subscription): float {
        throw new \LogicException('Not implemented.');
    }

}
