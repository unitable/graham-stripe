<?php

namespace Unitable\GrahamStripe\Cashier;

use Unitable\Graham\Support\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unitable\Graham\Coupon\Coupon;

/**
 * @property string $stripe_coupon_id
 * @property Coupon $coupon
 */
class StripeCoupon extends Model {

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Find by coupon id.
     *
     * @param int $coupon_id
     * @return static|null
     */
    public static function findByCouponId(int $coupon_id) {
        return static::query()->where('coupon_id', $coupon_id)->first();
    }

    /**
     * Find by Stripe coupon id.
     *
     * @param string $stripe_coupon_id
     * @return static|null
     */
    public static function findByStripeCouponId(string $stripe_coupon_id) {
        return static::query()->where('stripe_coupon_id', $stripe_coupon_id)->first();
    }

    /**
     * Get the coupon model.
     *
     * @return BelongsTo
     */
    public function coupon(): BelongsTo {
        return $this->belongsTo(Coupon::class);
    }

}
