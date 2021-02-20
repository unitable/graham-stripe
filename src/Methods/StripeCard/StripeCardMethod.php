<?php

namespace Unitable\GrahamStripe\Methods\StripeCard;

use Unitable\Graham\Method\Method;
use Unitable\GrahamStripe\Engines\Stripe\StripeEngine;

/**
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
