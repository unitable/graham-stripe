<?php

namespace Unitable\GrahamStripe\Engine\Concerns;

use Unitable\Graham\Subscription\Subscription;
use Unitable\GrahamStripe\Engine\SubscriptionInvoiceBuilder;

trait ManagesInvoices {

    /**
     * Create a new invoice.
     *
     * @param Subscription $subscription
     * @return SubscriptionInvoiceBuilder
     */
    public function newInvoice(Subscription $subscription): SubscriptionInvoiceBuilder {
        return new SubscriptionInvoiceBuilder($subscription);
    }

}
