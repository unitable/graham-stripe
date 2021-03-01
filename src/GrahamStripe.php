<?php

namespace Unitable\GrahamStripe;

use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;
use Stripe\SetupIntent;

class GrahamStripe {

    const VERSION = '0.1.0';

    /**
     * Get the Stripe options.
     *
     * @param array $options
     */
    public function stripeOptions(array $options = []): array {
        return Cashier::stripeOptions($options);
    }

    /**
     * Create a new SetupIntent instance.
     *
     * @param array $options
     * @return SetupIntent
     * @throws ApiErrorException
     */
    public function createSetupIntent(array $options = []): SetupIntent {
        return SetupIntent::create($options, $this->stripeOptions());
    }

}
