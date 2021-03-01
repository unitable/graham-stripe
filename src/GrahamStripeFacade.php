<?php

namespace Unitable\GrahamStripe;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array stripeOptions(array $options = []) Get the Stripe options.
 *
 * @see \Unitable\GrahamStripe\GrahamStripe
 */
class GrahamStripeFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'graham-stripe';
    }
}
