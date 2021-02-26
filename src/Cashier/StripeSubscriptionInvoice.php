<?php

namespace Unitable\GrahamStripe\Cashier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unitable\Graham\Subscription\SubscriptionInvoice;

/**
 * @property string $status
 * @property string $stripe_invoice_id
 * @property SubscriptionInvoice $subscription_invoice
 */
class StripeSubscriptionInvoice extends Model {

    const DRAFT = 'draft';
    const OPEN = 'open';
    const PAID = 'paid';
    const UNCOLLECTIBLE = 'uncollectible';
    const VOID = 'void';
    const DELETED = 'deleted';

    protected $guarded = [];

    /**
     * Translate Stripe status to Graham status.
     *
     * @return string
     */
    public function translateStatus(): string {
        $status = SubscriptionInvoice::PROCESSING;

        switch ($this->status) {
            case static::DRAFT:
                $status = SubscriptionInvoice::PROCESSING;
            break;
            case static::OPEN:
                $status = SubscriptionInvoice::OPEN;
            break;
            case static::PAID:
                $status = SubscriptionInvoice::PAID;
            break;
            case static::UNCOLLECTIBLE:
            case static::VOID:
            case static::DELETED:
                $status = SubscriptionInvoice::CANCELED;
            break;
        }

        return $status;
    }

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

    /**
     * Get the subscription invoice model.
     *
     * @return BelongsTo
     */
    public function subscription_invoice(): BelongsTo {
        return $this->belongsTo(SubscriptionInvoice::class);
    }

}
