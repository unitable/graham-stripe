<?php

namespace Unitable\GrahamStripe\Cashier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable as Cashier;

class StripeCustomer extends Model {

    use Cashier;

    protected $guarded = [];

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    /**
     * Find a Stripe customer by user id or create a new one.
     *
     * @param int $user_id
     * @return static
     */
    public static function findByUserIdOrCreate(int $user_id) {
        return static::query()->firstOrCreate([
            'user_id' => $user_id
        ]);
    }

    /**
     * Get the customer name.
     *
     * @return string|null
     */
    public function getNameAttribute(): ?string {
        return $this->user->name;
    }

    /**
     * Get the customer email.
     *
     * @return string|null
     */
    public function getEmailAttribute(): ?string {
        return $this->user->email;
    }

    /**
     * Get the user model.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        $model = config('graham-stripe.user');

        return $this->belongsTo($model);
    }

    /**
     * Get all of the subscriptions for the Stripe model.
     *
     * @return HasMany
     */
    public function subscriptions() {
        return $this->hasMany(StripeSubscription::class, $this->getForeignKey(), 'user_id')
            ->orderBy('created_at', 'desc');
    }

    public function getForeignKey() {
        return 'user_id';
    }

}
