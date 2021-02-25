<?php

namespace Unitable\GrahamStripe\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Unitable\GrahamStripe\Cashier\StripeSubscription;
use Unitable\GrahamStripe\Cashier\StripeSubscriptionInvoice;

class WebhookController extends Controller {

    /**
     * Handle invoice created.
     *
     * @param array $payload
     * @return Response
     */
    public function handleInvoiceCreated(array $payload): Response {
        $data = $payload['data']['object'];

        if (empty($data['subscription']))
            return $this->successMethod();

        if ($stripe_subscription = StripeSubscription::findByStripeSubscriptionId($data['subscription'])) {
            $subscription = $stripe_subscription->subscription;

            $invoice = $subscription->newInvoice()->create();

            StripeSubscriptionInvoice::create([
                'subscription_invoice_id' => $invoice->id,
                'status' => $data['status'],
                'stripe_invoice_id' => $data['id'],
                'currency_code' => strtoupper($data['currency']),
                'total' => $data['amount_due'] / 100
            ]);
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice updated.
     *
     * @param array $payload
     * @return Response
     */
    public function handleInvoiceUpdated(array $payload): Response {
        $data = $payload['data']['object'];

        if ($invoice = StripeSubscriptionInvoice::findByStripeInvoiceId($data['id'])) {
            $invoice->update([
                'status' => $data['status'],
                'currency_code' => strtoupper($data['currency']),
                'total' => $data['amount_due'] / 100
            ]);
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice deleted.
     *
     * @param array $payload
     * @return Response
     */
    public function handleInvoiceDeleted(array $payload): Response {
        $data = $payload['data']['object'];

        if ($invoice = StripeSubscriptionInvoice::findByStripeInvoiceId($data['id'])) {
            $invoice->delete();
        }

        return $this->successMethod();
    }

}
