<?php

namespace Buyback\Tests\Providers;

use Buyback\Providers\BuybackProvider;
use TradeAppOne\Tests\TestCase;

class BuybackProviderTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $class = new BuybackProvider(app());

        $className = get_class($class);

        $this->assertEquals(BuybackProvider::class, $className);
    }
}
