<?php

namespace Unitable\GrahamStripe\Http\Controllers;

use Illuminate\Support\Carbon;
use Laravel\Cashier\Http\Controllers\WebhookController as Controller;
use Symfony\Component\HttpFoundation\Response;

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

        if ($subscription = $this->getSubscriptionByStripeId($data['subscription'])) {
            $total = $data['amount_due'] / 100;

            $invoice = $subscription->newInvoice()
                ->status($this->resolveInvoiceStatusFromStripe($data['status']))
                ->value($total, $data['currency'])
                ->linkStripeInvoice($data['id']);

            if ($data['due_date']) {
                $due_at = Carbon::createFromTimestamp($data['due_date']);

                $invoice->dueAt($due_at);
            }

            $invoice->create();
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

        if ($invoice = SubscriptionInvoice::findByStripeId($data['id'])) {
            $total = $data['amount_due'] / 100;

            $invoice->update([
                'status' => $this->resolveInvoiceStatusFromStripe($data['status']),
                'currency_total' => $total,
                'total' => $total * $invoice->currency_value
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

        if ($invoice = SubscriptionInvoice::findByStripeId($data['id'])) {
            $invoice->delete();
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice payment failed.
     *
     * @param array $payload
     * @return Response
     */
    public function handleInvoicePaymentFailed(array $payload): Response {
        $data = $payload['data']['object'];

        if ($invoice = SubscriptionInvoice::findByStripeId($data['id'])) {
            $invoice->update([
                'payment_failed' => true
            ]);
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice payment succeeded.
     *
     * @param array $payload
     * @return Response
     */
    public function handleInvoicePaymentSucceeded(array $payload): Response {
        $data = $payload['data']['object'];

        if ($invoice = SubscriptionInvoice::findByStripeId($data['id'])) {
            $invoice->update([
                'payment_failed' => false
            ]);
        }

        return $this->successMethod();
    }

    /**
     * Get the subscription entity instance by Stripe ID.
     *
     * @param string $stripe_id
     * @return Subscription|null
     */
    protected function getSubscriptionByStripeId(string $stripe_id): ?Subscription {
        $query = StripeSubscription::query();

        /** @var StripeSubscription $subscription */
        $subscription = $query
            ->where('stripe_id', $stripe_id)
            ->first();

        return $subscription ?
            $subscription->subscription : null;
    }

    /**
     * Resolve invoice status from Stripe invoice status.
     *
     * @param string $stripe_status
     * @return string
     */
    protected function resolveInvoiceStatusFromStripe(string $stripe_status): string {
        $status = null;

        switch ($stripe_status) {
            case StripeInvoiceStatuses::DRAFT:
                $status = InvoiceStatuses::CREATED;
                break;
            case StripeInvoiceStatuses::OPEN:
                $status = InvoiceStatuses::OPEN;
                break;
            case StripeInvoiceStatuses::PAID:
                $status = InvoiceStatuses::PAID;
                break;
            case StripeInvoiceStatuses::UNCOLLECTIBLE:
                $status = InvoiceStatuses::UNCOLLECTIBLE;
                break;
            case StripeInvoiceStatuses::VOID:
                $status = InvoiceStatuses::VOID;
                break;
            case StripeInvoiceStatuses::DELETED:
                $status = InvoiceStatuses::DELETED;
                break;
        }

        return $status;
    }

}
