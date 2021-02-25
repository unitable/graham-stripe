<?php

namespace Unitable\GrahamStripe\Http\Controllers;

use Illuminate\Support\Carbon;
use Laravel\Cashier\Http\Controllers\WebhookController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Unitable\GrahamStripe\Cashier\StripeSubscription;
use Unitable\GrahamStripe\Cashier\StripeSubscriptionInvoice;

class WebhookController extends Controller {

    /**
     * @param array $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionCreated(array $payload): Response {
        throw new \LogicException('Not implemented.');
    }

    /**
     * Handle customer subscription updated.
     *
     * @param array $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionUpdated(array $payload) {
        $data = $payload['data']['object'];

        if ($stripe_subscription = StripeSubscription::findByStripeSubscriptionId($data['id'])) {
            if (isset($data['status'])
                && $data['status'] === StripeSubscription::INCOMPLETE_EXPIRED) {
                $stripe_subscription->items()->delete();
                $stripe_subscription->delete();

                return $this->successMethod();
            }

            $firstItem = $data['items']['data'][0];
            $isSinglePlan = count($data['items']['data']) === 1;

            // Plan...
            $stripe_subscription->stripe_plan = $isSinglePlan ? $firstItem['plan']['id'] : null;

            // Quantity...
            $stripe_subscription->quantity = $isSinglePlan && isset($firstItem['quantity']) ? $firstItem['quantity'] : null;

            // Trial ending date...
            if (isset($data['trial_end'])) {
                $trialEnd = Carbon::createFromTimestamp($data['trial_end']);

                if (! $stripe_subscription->trial_ends_at || $stripe_subscription->trial_ends_at->ne($trialEnd)) {
                    $stripe_subscription->trial_ends_at = $trialEnd;
                }
            }

            // Period ending date...
            if (isset($data['current_period_end'])) {
                $periodEnd = Carbon::createFromTimestamp($data['current_period_end']);

                $stripe_subscription->period_ends_at = $periodEnd;
            }

            // Cancellation date...
            if (isset($data['cancel_at_period_end'])) {
                if ($data['cancel_at_period_end']) {
                    $stripe_subscription->ends_at = $stripe_subscription->onTrial()
                        ? $stripe_subscription->trial_ends_at
                        : Carbon::createFromTimestamp($data['current_period_end']);
                } else {
                    $stripe_subscription->ends_at = null;
                }
            }

            // Status...
            if (isset($data['status'])) {
                $stripe_subscription->stripe_status = $data['status'];
            }

            $stripe_subscription->save();

            // Update subscription items...
            if (isset($data['items'])) {
                $plans = [];

                foreach ($data['items']['data'] as $item) {
                    $plans[] = $item['plan']['id'];

                    $stripe_subscription->items()->updateOrCreate([
                        'stripe_id' => $item['id'],
                    ], [
                        'stripe_plan' => $item['plan']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]);
                }

                // Delete items that aren't attached to the subscription anymore...
                $stripe_subscription->items()->whereNotIn('stripe_plan', $plans)->delete();
            }
        }

        return $this->successMethod();
    }

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