<?php

namespace Unitable\GrahamStripe\Methods\StripeCard;

use Unitable\GrahamStripe\Engine\StripeEngine;
use Unitable\GrahamStripe\Method\StripeMethod;

/**
 * @property int $user_id
 * @property string $stripe_setup_intent_id
 * @property string $stripe_payment_method_id
 * @property StripeEngine $engine
 */
class StripeCardMethod extends StripeMethod {

    protected $guarded = [];

    /**
     * Get the engine.
     *
     * @return StripeEngine
     */
    public function getEngineAttribute(): StripeEngine {
        return app()->make(StripeEngine::class);
    }

}
