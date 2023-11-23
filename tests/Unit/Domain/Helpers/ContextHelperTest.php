<?php

namespace TradeAppOne\Tests\Unit\Domain\Helpers;

use TradeAppOne\Tests\TestCase;
use TradeAppOne\Domain\Components\Helpers\ContextHelper;
use TradeAppOne\Domain\Enumerators\ContextEnum;

class ContextHelperTest extends TestCase
{
    /** @test */
    public function should_return_context_all()
    {
        $permissions = [ 'API' => [ 'SALE' => ['CONTEXT_ALL']]];
        $context     = ContextHelper::getContext($permissions, 'API', 'SALE');

        $this->assertEquals(ContextEnum::CONTEXT_ALL, $context);
    }

    /** @test */
    public function should_return_context_NONEXISTENT()
    {
        $permissions = [ 'API' => [ 'SALE' => ['CONTEXT_AU']]];
        $context     = ContextHelper::getContext($permissions, 'API', 'SALE');

        $this->assertEquals(ContextEnum::CONTEXT_NON_EXISTENT, $context);
    }

    /** @test */
    public function should_return_context_non_existent()
    {
        $permissions = null;
        $context     = ContextHelper::getContext($permissions, 'API', 'SALE');

        $this->assertEquals(ContextEnum::CONTEXT_NON_EXISTENT, $context);
    }

    /** @test */
    public function should_return_context_hierarchy()
    {
        $permissions = [ 'API' => [ 'SALE' => ['CONTEXT_HIERARCHY']]];
        $context     = ContextHelper::getContext($permissions, 'API', 'SALE');

        $this->assertEquals(ContextEnum::CONTEXT_HIERARCHY, $context);
    }

    /** @test */
    public function should_return_context_network()
    {
        $permissions = [ 'API' => [ 'SALE' => ['CONTEXT_NETWORK']]];
        $context     = ContextHelper::getContext($permissions, 'API', 'SALE');

        $this->assertEquals(ContextEnum::CONTEXT_NETWORK, $context);
    }

    /** @test */
    public function should_return_context_network_many()
    {
        $permissions = [ 'API' => [ 'SALE' => ['CONTEXT_NETWORK', 'CONTEXT_HIERARCHY']]];
        $context     = ContextHelper::getContext($permissions, 'API', 'SALE');

        $this->assertEquals(ContextEnum::CONTEXT_NETWORK, $context);
    }
}
