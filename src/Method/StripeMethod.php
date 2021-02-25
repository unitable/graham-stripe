<?php

namespace Unitable\GrahamStripe\Method;

use Unitable\Graham\Method\Method;

/**
 * @property string $stripe_payment_method_id
 */
abstract class StripeMethod extends Method {

    /**
     * Get the Stripe payment method id.
     *
     * @return string
     */
    public function getStripePaymentMethodId(): string {
        return $this->stripe_payment_method_id;
    }

}
