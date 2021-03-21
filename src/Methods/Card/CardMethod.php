<?php

namespace Unitable\GrahamStripe\Methods\Card;

use Unitable\GrahamStripe\Engine\StripeEngine;
use Unitable\GrahamStripe\Method\StripeMethod;

/**
 * @property int $user_id
 * @property string $stripe_setup_intent_id
 * @property string $stripe_payment_method_id
 * @property StripeEngine $engine
 */
final class CardMethod extends StripeMethod {

    protected $table = 'stripe_card_methods';

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
