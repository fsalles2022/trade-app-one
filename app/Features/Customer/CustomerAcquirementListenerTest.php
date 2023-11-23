<?php

namespace TradeAppOne\Features\Customer;

use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use TradeAppOne\Tests\Helpers\SaleHelper;
use TradeAppOne\Tests\TestCase;

class CustomerAcquirementListenerTest extends TestCase
{
    use SaleHelper, SivFactoriesHelper;

    /** @test */
    public function should_return_an_instance()
    {
        $customerService = new CustomerAcquirementListener();
        $className       = get_class($customerService);
        $this->assertEquals(CustomerAcquirementListener::class, $className);
    }
}
