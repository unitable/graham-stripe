<?php

namespace Unitable\GrahamStripe\Http\Controllers;

use Illuminate\Support\Carbon;
use Laravel\Cashier\Http\Controllers\WebhookController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Unitable\Graham\GrahamFacade as Graham;
use Unitable\Graham\Subscription\SubscriptionInvoice;
use Unitable\Graham\Subscription\SubscriptionInvoiceDiscount;
use Unitable\GrahamStripe\Cashier\StripeCoupon;
use Unitable\GrahamStripe\Cashier\StripeDiscount;
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
            $currency = Graham::currency(strtoupper($data['currency']));

            if ($subscription->onTrial()
                && $data['billing_reason'] === 'subscription_create'
                && $data['subtotal'] === 0.00 && $data['total'] === 0.00) {
                return $this->successMethod();
            }

            $invoice = SubscriptionInvoice::create([
                'status' => SubscriptionInvoice::PROCESSING,
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'plan_id' => $subscription->plan_id,
                'plan_price_id' => $subscription->plan_price_id,
                'method' => get_class($subscription->method),
                'method_id' => $subscription->method->id,
                'engine' => get_class($subscription->engine),
                'currency_code' => $currency->code,
                'currency_rate' => $currency->rate,
                'subtotal' => $data['subtotal'] / 100,
                'total' => $data['total'] / 100,
                'due_at' => $data['due_date'] ?
                    Carbon::createFromTimestamp($data['due_date']) : $subscription->period_ends_at
            ]);

            $discounts = collect($data['total_discount_amounts']);

            if ($coupon_discount = $data['discount']) {
                $discount = $discounts->first(fn($discount) =>
                    $discount['discount'] === $coupon_discount['id']);
                $discounts = $discounts->filter(fn($discount) =>
                    $discount['discount'] !== $coupon_discount['id']);

                $stripe_coupon = StripeCoupon::findByStripeCouponId($coupon_discount['coupon']['id']);
                if (!$stripe_coupon) throw new \RuntimeException('Stripe coupon not found.');

                SubscriptionInvoiceDiscount::create([
                    'subscription_invoice_id' => $invoice->id,
                    'subscription_id' => $subscription->id,
                    'discount_type' => get_class($stripe_coupon->coupon),
                    'discount_id' => $stripe_coupon->coupon->id,
                    'value' => $discount['amount'] / 100
                ]);
            }

            foreach ($discounts as $discount) {
                SubscriptionInvoiceDiscount::create([
                    'subscription_invoice_id' => $invoice->id,
                    'subscription_id' => $subscription->id,
                    'discount_type' => StripeDiscount::class,
                    'value' => $discount['amount'] / 100
                ]);
            }

            /** @var StripeSubscriptionInvoice $stripe_invoice */
            $stripe_invoice = StripeSubscriptionInvoice::create([
                'stripe_subscription_id' => $stripe_subscription->id,
                'subscription_invoice_id' => $invoice->id,
                'subscription_id' => $subscription->id,
                'status' => $data['status'],
                'stripe_invoice_id' => $data['id'],
                'currency_code' => $currency->code,
                'currency_rate' => $currency->rate,
                'total' => $data['total'] / 100
            ]);

            $invoice->update([
                'status' => $new_status = $stripe_invoice->translateStatus(),
                'paid_at' => $new_status === StripeSubscriptionInvoice::PAID ? now() : null
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
                'status' => $data['status']
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
            $invoice->subscription_invoice->delete();

            $invoice->delete();
        }

        return $this->successMethod();
    }

}
