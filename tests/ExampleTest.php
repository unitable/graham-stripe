<?php

namespace Unitable\GrahamStripe\Tests;

use Orchestra\Testbench\TestCase;
use Unitable\GrahamStripe\GrahamStripeServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [GrahamStripeServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
