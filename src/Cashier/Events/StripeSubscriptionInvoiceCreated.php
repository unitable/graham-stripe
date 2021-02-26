<?php

namespace Unitable\GrahamStripe\Cashier\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Unitable\GrahamStripe\Cashier\StripeSubscriptionInvoice;

class StripeSubscriptionInvoiceCreated {

    use Dispatchable;

    /**
     * The event invoice.
     *
     * @var StripeSubscriptionInvoice
     */
    public StripeSubscriptionInvoice $stripe_invoice;

    /**
     * Create a new event instance.
     *
     * @param StripeSubscriptionInvoice $stripe_invoice
     */
    public function __construct(StripeSubscriptionInvoice $stripe_invoice) {
        $this->stripe_invoice = $stripe_invoice;
    }

}
