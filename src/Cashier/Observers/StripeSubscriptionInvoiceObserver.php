<?php

namespace Unitable\GrahamStripe\Cashier\Observers;


use Unitable\GrahamStripe\Cashier\Events\StripeSubscriptionInvoiceCreated;
use Unitable\GrahamStripe\Cashier\Events\StripeSubscriptionInvoiceUpdated;
use Unitable\GrahamStripe\Cashier\StripeSubscriptionInvoice;

class StripeSubscriptionInvoiceObserver {

    /**
     * Handle the Stripe subscription "created" event.
     *
     * @param StripeSubscriptionInvoice $stripe_invoice
     * @return void
     */
    public function created(StripeSubscriptionInvoice $stripe_invoice) {
        StripeSubscriptionInvoiceCreated::dispatch($stripe_invoice);
    }

    /**
     * Handle the Stripe subscription "updated" event.
     *
     * @param StripeSubscriptionInvoice $stripe_invoice
     * @return void
     */
    public function updated(StripeSubscriptionInvoice $stripe_invoice) {
        StripeSubscriptionInvoiceUpdated::dispatch($stripe_invoice);
    }

}
