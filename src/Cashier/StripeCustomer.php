<?php

namespace Unitable\GrahamStripe\Cashier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable as Cashier;

class StripeCustomer extends Model {

    use Cashier;

    protected $guarded = [];

    protected $primaryKey = 'user_id';

    /**
     * Find a Stripe customer by user id or create a new one.
     *
     * @param int $user_id
     * @return static
     */
    public static function findByUserIdOrNew(int $user_id) {
        return static::query()->firstOrCreate([
            'user_id' => $user_id
        ]);
    }

    /**
     * Get all of the subscriptions for the Stripe model.
     *
     * @return HasMany
     */
    public function subscriptions() {
        return $this->hasMany(StripeSubscription::class, 'user_id', 'user_id')
            ->orderBy('created_at', 'desc');
    }

}
