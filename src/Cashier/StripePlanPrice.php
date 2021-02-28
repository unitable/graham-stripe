<?php

namespace Unitable\GrahamStripe\Cashier;

use Unitable\Graham\Support\Model;

/**
 * @property string $stripe_price_id
 */
class StripePlanPrice extends Model {

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Find by plan price id.
     *
     * @param int $plan_price_id
     * @return static|null
     */
    public static function findByPlanPriceId(int $plan_price_id) {
        return static::query()->where('plan_price_id', $plan_price_id)->first();
    }

}
