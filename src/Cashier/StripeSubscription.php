<?php

namespace Unitable\GrahamStripe\Cashier;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Subscription as CashierSubscription;
use Unitable\Graham\Subscription\Subscription;

/**
 * @property string $stripe_status
 * @property Subscription|null $subscription
 */
class StripeSubscription extends CashierSubscription {

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

}
