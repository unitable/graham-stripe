<?php

namespace Unitable\GrahamStripe\Cashier;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $stripe_price_id
 */
class StripeSubscriptionInvoice extends Model {

    protected $guarded = [];

    /**
     * Find by subscription invoice id.
     *
     * @param int $subscription_invoice_id
     * @return static|null
     */
    public static function findBySubscriptionInvoiceId(int $subscription_invoice_id) {
        return static::query()->where('subscription_invoice_id', $subscription_invoice_id)->first();
    }

    /**
     * Find by Stripe invoice id.
     *
     * @param int $stripe_invoice_id
     * @return static|null
     */
    public static function findByStripeInvoiceId(int $stripe_invoice_id) {
        return static::query()->where('stripe_invoice_id', $stripe_invoice_id)->first();
    }

}
