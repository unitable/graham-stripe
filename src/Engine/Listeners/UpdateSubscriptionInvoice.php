<?php

namespace Unitable\GrahamStripe\Engine\Listeners;

use Unitable\GrahamStripe\Cashier\Events\StripeSubscriptionInvoiceUpdated;

class UpdateSubscriptionInvoice {

    /**
     * Handle the event.
     *
     * @param StripeSubscriptionInvoiceUpdated $event
     * @return void
     */
    public function handle(StripeSubscriptionInvoiceUpdated $event) {
        $stripe_invoice = $event->stripe_invoice;

        if ($invoice = $stripe_invoice->subscription_invoice) {
            $invoice->status = $stripe_invoice->translateStatus();

            $invoice->save();
        }
    }

}
