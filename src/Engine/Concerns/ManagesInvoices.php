<?php

namespace Unitable\GrahamStripe\Engine\Concerns;

use Unitable\Graham\Engine\Contracts\SubscriptionInvoiceBuilder;
use Unitable\Graham\Subscription\Subscription;

trait ManagesInvoices {

    /**
     * Create a new invoice.
     *
     * @param Subscription $subscription
     * @return SubscriptionInvoiceBuilder
     */
    public function newInvoice(Subscription $subscription): SubscriptionInvoiceBuilder {
        throw new \LogicException('Not implemented.');
    }

}
