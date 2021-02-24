<?php

namespace Unitable\GrahamStripe\Methods\StripeCard;

use Unitable\Graham\Method\Method;
use Unitable\GrahamStripe\Engine\StripeEngine;

/**
 * @property int $user_id
 * @property string $stripe_setup_intent_id
 * @property string $stripe_payment_method_id
 * @property StripeEngine $engine
 */
class StripeCardMethod extends Method {

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
