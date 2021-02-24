<?php

namespace Unitable\GrahamStripe\Cashier\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Unitable\GrahamStripe\Cashier\StripeInvoice;

class StripeInvoiceUpdated {

    use Dispatchable;

    /**
     * The event invoice.
     *
     * @var StripeInvoice
     */
    public StripeInvoice $stripe_invoice;

    /**
     * Create a new event instance.
     *
     * @param StripeInvoice $stripe_invoice
     */
    public function __construct(StripeInvoice $stripe_invoice) {
        $this->stripe_invoice = $stripe_invoice;
    }

}
