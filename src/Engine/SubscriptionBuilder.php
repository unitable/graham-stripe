<?php

namespace Unitable\GrahamStripe\Engine;

use Unitable\Graham\Engine\SubscriptionBuilder as Builder;
use Unitable\Graham\Subscription\Subscription;
use Unitable\GrahamStripe\Cashier\StripeCoupon;
use Unitable\GrahamStripe\Cashier\StripeCustomer;
use Unitable\GrahamStripe\Cashier\StripePlanPrice;

class SubscriptionBuilder extends Builder {

    /**
     * Create the subscription.
     *
     * @return Subscription
     */
    public function create(): Subscription {
        $customer = StripeCustomer::findByUserIdOrNew($this->owner->id);

        $plan_price = StripePlanPrice::findByPlanPriceId($this->plan_price->id);
        if (!$plan_price) throw new \RuntimeException('Stripe plan price not found.');

        $builder = $customer
            ->newSubscription($this->plan->id, $plan_price->stripe_price_id);

        if (isset($this->trial_days))
            $builder->trialDays($this->trial_days);

        if (isset($this->coupon)) {
            $coupon = StripeCoupon::findByCouponId($this->coupon->id);
            if (!$coupon) throw new \RuntimeException('Stripe coupon not found.');

            $builder->withCoupon($coupon->stripe_coupon_id);
        }

        return $builder->create()->subscription;
    }

}
