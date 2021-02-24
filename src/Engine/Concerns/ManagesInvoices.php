<?php

namespace Unitable\GrahamStripe\Engine\Concerns;

use Unitable\Graham\Engine\Contracts\SubscriptionInvoiceBuilder;
use Unitable\Graham\Subscription\Subscription;

trait ManagesInvoices {

    public function newInvoice(Subscription $subscription): SubscriptionInvoiceBuilder {
        // TODO: Implement newInvoice() method.
    }

}
