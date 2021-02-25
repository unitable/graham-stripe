<?php

namespace Unitable\GrahamStripe\Cashier;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Subscription as CashierSubscription;
use Unitable\Graham\Subscription\Subscription;

/**
 * @property string $stripe_status
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $period_ends_at
 * @property Carbon|null $ends_at
 * @property Subscription|null $subscription
 */
class StripeSubscription extends CashierSubscription {

    const ACTIVE = 'active';
    const PAST_DUE = 'past_due';
    const UNPAID = 'unpaid';
    const CANCELED = 'canceled';
    const INCOMPLETE = 'incomplete';
    const INCOMPLETE_EXPIRED = 'incomplete_expired';
    const TRIALING = 'trialing';

    protected $dates = [
        'trial_ends_at',
        'period_ends_at',
        'ends_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Translate Stripe status to Graham status.
     *
     * @return string
     */
    public function translateStatus(): string {
        $status = Subscription::PROCESSING;

        switch ($this->stripe_status) {
            case static::TRIALING:
                $status = Subscription::TRIAL;
            break;
            case static::ACTIVE:
                $status = Subscription::ACTIVE;
            break;
            case static::PAST_DUE:
            case static::UNPAID:
            case static::INCOMPLETE:
                $status = Subscription::INCOMPLETE;
            break;
            case static::CANCELED:
            case static::INCOMPLETE_EXPIRED:
                $status = Subscription::CANCELED;
            break;
        }

        return $status;
    }

    /**
     * Get the subscription items related to the subscription.
     *
     * @return HasMany
     */
    public function items() {
        return $this->hasMany(StripeSubscriptionItem::class, 'subscription_id');
    }

    /**
     *  Get the subscription model.
     *
     * @return BelongsTo
     */
    public function subscription(): BelongsTo {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Find by subscription id.
     *
     * @param int $subscription_id
     * @return static|null
     */
    public static function findBySubscriptionId(int $subscription_id) {
        return static::query()->where('subscription_id', $subscription_id)->first();
    }

    /**
     * Find by Stripe subscription id.
     *
     * @param string $stripe_subscription_id
     * @return static|null
     */
    public static function findByStripeSubscriptionId(string $stripe_subscription_id) {
        return static::query()->where('stripe_id', $stripe_subscription_id)->first();
    }

}
