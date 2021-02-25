<?php

namespace Unitable\GrahamStripe\Engine;

use Illuminate\Support\Carbon;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Laravel\Cashier\Exceptions\PaymentFailure;
use Unitable\Graham\Engine\SubscriptionBuilder as Builder;
use Unitable\Graham\Plan\Plan;
use Unitable\Graham\Plan\PlanPrice;
use Unitable\Graham\Subscription\Subscription;
use Unitable\GrahamStripe\Cashier\StripeCoupon;
use Unitable\GrahamStripe\Cashier\StripeCustomer;
use Unitable\GrahamStripe\Cashier\StripePlanPrice;
use Unitable\GrahamStripe\Cashier\StripeSubscription;
use Unitable\GrahamStripe\Method\StripeMethod;

/**
 * @property StripeMethod $method
 */
class SubscriptionBuilder extends Builder {

    /**
     * SubscriptionBuilder constructor.
     *
     * @param $owner
     * @param Plan $plan
     * @param StripeMethod $method
     * @param PlanPrice|null $plan_price
     */
    public function __construct($owner, Plan $plan, StripeMethod $method, ?PlanPrice $plan_price = null) {
        parent::__construct($owner, $plan, $method, $plan_price);
    }

    /**
     * Create the subscription.
     *
     * @return Subscription
     * @throws PaymentActionRequired
     * @throws PaymentFailure
     */
    public function create(): Subscription {
        $subscription = parent::create();

        $stripe_subscription = null;
        try {
            $stripe_subscription = $this->createStripe($subscription);
        } catch (\Exception $e) {
            $subscription->delete();

            throw $e;
        }

        $subscription->status = $stripe_subscription->translateStatus();
        $subscription->trial_ends_at = $stripe_subscription->trial_ends_at;
        $subscription->period_ends_at = $stripe_subscription->period_ends_at;
        $subscription->ends_at = $stripe_subscription->ends_at;

        $subscription->save();

        $stripe_subscription->update([
            'subscription_id' => $subscription->id
        ]);

        return $subscription;
    }

    /**
     * Create the Stripe subscription.
     *
     * @param Subscription $subscription
     * @return StripeSubscription
     * @throws PaymentActionRequired
     * @throws PaymentFailure
     */
    protected function createStripe(Subscription $subscription): StripeSubscription {
        $customer = StripeCustomer::findByUserIdOrCreate($this->owner->id);

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

        /** @var StripeSubscription $stripe_subscription */
        $stripe_subscription = $builder->create($this->method->getStripePaymentMethodId());
        $stripe_data = $stripe_subscription->asStripeSubscription();

        $stripe_subscription->period_ends_at = Carbon::createFromTimestamp($stripe_data->current_period_end);
        $stripe_subscription->save();

        return $stripe_subscription;
    }

}
