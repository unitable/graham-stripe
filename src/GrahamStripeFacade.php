<?php

namespace Unitable\GrahamStripe;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Unitable\GrahamStripe\Skeleton\SkeletonClass
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
