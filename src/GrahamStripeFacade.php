<?php

namespace Unitable\GrahamStripe;

use Illuminate\Support\Facades\Facade;
use Stripe\SetupIntent;

/**
 * @method static array stripeOptions(array $options = []) Get the Stripe options.
 * @method static SetupIntent createSetupIntent(array $options = []) Create a new SetupIntent instance.
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
